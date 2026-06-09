@extends('layouts.app')

@section('content')
<h1>Eventos</h1>

<div id="evento-modal" class="modal-backdrop" style="display:none">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="margin:0">Editar Evento</h3>
            <button type="button" id="evento-modal-close" class="secondary">✕</button>
        </div>
        <form id="evento-edit-form" style="margin-top:16px">
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label for="edit-titulo">Titulo</label>
                <input id="edit-titulo" type="text" required>
            </div>
            <div class="form-group">
                <label for="edit-descricao">Descricao</label>
                <textarea id="edit-descricao" rows="2"></textarea>
            </div>
            <div class="grid two">
                <div class="form-group">
                    <label for="edit-data">Data</label>
                    <input id="edit-data" type="date" required>
                </div>
                <div class="form-group">
                    <label for="edit-horario">Horario</label>
                    <input id="edit-horario" type="time" required>
                </div>
            </div>
            <div class="form-group">
                <label for="edit-local">Local</label>
                <input id="edit-local" type="text" required>
            </div>
            <div class="grid two">
                <div class="form-group">
                    <label for="edit-vagas">Qtd. Vagas</label>
                    <input id="edit-vagas" type="number" min="1" required>
                </div>
                <div class="form-group">
                    <label for="edit-status">Status</label>
                    <select id="edit-status">
                        <option value="aberto">Aberto</option>
                        <option value="encerrado">Encerrado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <div id="evento-edit-message" class="message"></div>
                <button type="submit">Salvar alteracoes</button>
            </div>
        </form>
    </div>
</div>

<div class="grid two">
    <div class="card">
        <h2>Novo evento</h2>
        <form id="evento-form">
            <div class="form-group">
                <label for="titulo">Titulo</label>
                <input id="titulo" type="text" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descricao</label>
                <textarea id="descricao" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label for="data">Data</label>
                <input id="data" type="date" required>
            </div>
            <div class="form-group">
                <label for="horario">Horario</label>
                <input id="horario" type="time" required>
            </div>
            <div class="form-group">
                <label for="local">Local</label>
                <input id="local" type="text" required>
            </div>
            <div class="form-group">
                <label for="quantidade_vagas">Quantidade de vagas</label>
                <input id="quantidade_vagas" type="number" min="1" required>
            </div>
            <button type="submit">Salvar</button>
            <div id="evento-form-message" class="message"></div>
        </form>
    </div>
    <div class="card">
        <h2>Inscritos em um evento</h2>
        <form id="participantes-evento-form">
            <div class="form-group">
                <label for="evento_id_busca">Evento</label>
                <select id="evento_id_busca" required>
                    <option value="">Selecione um evento</option>
                </select>
            </div>
            <button type="submit">Buscar</button>
            <div id="participantes-evento-message" class="message"></div>
        </form>
        <div id="participantes-evento-list"></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Lista de eventos</h2>
        <button id="eventos-refresh" class="secondary" type="button">Atualizar</button>
    </div>
    <div id="eventos-message" class="message"></div>
    <table class="table" id="eventos-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titulo</th>
                <th>Data</th>
                <th>Horario</th>
                <th>Local</th>
                <th>Vagas</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    const apiBase = '/api';
    const csrfToken = document.querySelector('#csrf-token')?.content;
    const eventosTableBody = document.querySelector('#eventos-table tbody');
    const eventosMessage = document.getElementById('eventos-message');
    const eventoFormMessage = document.getElementById('evento-form-message');
    const participantesMessage = document.getElementById('participantes-evento-message');
    const participantesList = document.getElementById('participantes-evento-list');
    const eventoBuscaSelect = document.getElementById('evento_id_busca');
    const eventoModal = document.getElementById('evento-modal');
    const eventoEditMessage = document.getElementById('evento-edit-message');

    const statusBadge = (status) => {
        const cls = {
            aberto: 'badge-success',
            encerrado: 'badge-gray',
            cancelado: 'badge-danger'
        };
        return `<span class="badge ${cls[status] || ''}">${status}</span>`;
    };

    const vagasCell = (disp, total) =>
        `<span class="${disp === 0 ? 'vagas-esgotadas' : ''}">${disp}/${total}</span>`;

    const setMessage = (el, text, type) => {
        el.textContent = text || '';
        el.className = type ? `message ${type}` : 'message';
    };

    const request = async (url, options = {}) => {
        const res = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(csrfToken ? {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                } : {})
            },
            ...options
        });
        if (res.status === 204) return null;
        const data = await res.json().catch(() => null);
        if (!res.ok) throw new Error(data?.message || 'Erro na requisicao');
        return data;
    };

    const updateEventoBuscaOptions = (eventos) => {
        const options = ['<option value="">Selecione um evento</option>'];

        for (const evento of eventos) {
            options.push(`<option value="${evento.id}">${evento.titulo} - ${evento.data || 'sem data'}</option>`);
        }

        eventoBuscaSelect.innerHTML = options.join('');
        eventoBuscaSelect.disabled = eventos.length === 0;
    };

    const loadEventos = async () => {
        try {
            setMessage(eventosMessage, '');
            const data = await request(`${apiBase}/eventos?per_page=100`);
            const items = data?.data || [];

            updateEventoBuscaOptions(items);

            eventosTableBody.innerHTML = items.map(e => `
                    <tr>
                        <td>${e.id}</td>
                        <td>${e.titulo}</td>
                        <td>${e.data || ''}</td>
                        <td>${e.horario || ''}</td>
                        <td>${e.local || ''}</td>
                        <td>${vagasCell(e.vagas_disponiveis, e.quantidade_vagas)}</td>
                        <td>${statusBadge(e.status)}</td>
                        <td class="td-actions">
                            <button class="secondary btn-sm" data-edit='${JSON.stringify(e).replace(/'/g, "&#39;")}'>Editar</button>
                            <button class="danger btn-sm" data-delete="${e.id}">Excluir</button>
                        </td>
                    </tr>
                `).join('');
        } catch (err) {
            setMessage(eventosMessage, err.message, 'error');
        }
    };

    eventosTableBody.addEventListener('click', async (event) => {
        const el = event.target;
        if (el.dataset.edit) {
            const e = JSON.parse(el.dataset.edit);
            document.getElementById('edit-id').value = e.id;
            document.getElementById('edit-titulo').value = e.titulo;
            document.getElementById('edit-descricao').value = e.descricao || '';
            document.getElementById('edit-data').value = e.data || '';
            document.getElementById('edit-horario').value = e.horario || '';
            document.getElementById('edit-local').value = e.local;
            document.getElementById('edit-vagas').value = e.quantidade_vagas;
            document.getElementById('edit-status').value = e.status;
            setMessage(eventoEditMessage, '');
            eventoModal.style.display = 'flex';
        }
        if (el.dataset.delete) {
            if (!confirm('Excluir este evento?')) return;
            try {
                await request(`${apiBase}/eventos/${el.dataset.delete}`, {
                    method: 'DELETE'
                });
                setMessage(eventosMessage, 'Evento excluido.', 'success');
                loadEventos();
            } catch (err) {
                setMessage(eventosMessage, err.message, 'error');
            }
        }
    });

    document.getElementById('evento-modal-close').addEventListener('click', () => eventoModal.style.display = 'none');
    eventoModal.addEventListener('click', e => {
        if (e.target === eventoModal) eventoModal.style.display = 'none';
    });

    document.getElementById('evento-edit-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const id = document.getElementById('edit-id').value;
        const payload = {
            titulo: document.getElementById('edit-titulo').value,
            descricao: document.getElementById('edit-descricao').value || null,
            data: document.getElementById('edit-data').value,
            horario: document.getElementById('edit-horario').value,
            local: document.getElementById('edit-local').value,
            quantidade_vagas: Number(document.getElementById('edit-vagas').value),
            status: document.getElementById('edit-status').value,
        };
        try {
            await request(`${apiBase}/eventos/${id}`, {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
            setMessage(eventoEditMessage, 'Salvo com sucesso!', 'success');
            loadEventos();
            setTimeout(() => eventoModal.style.display = 'none', 900);
        } catch (err) {
            setMessage(eventoEditMessage, err.message, 'error');
        }
    });

    document.getElementById('eventos-refresh').addEventListener('click', loadEventos);

    document.getElementById('evento-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const payload = {
            titulo: document.getElementById('titulo').value,
            descricao: document.getElementById('descricao').value || null,
            data: document.getElementById('data').value,
            horario: document.getElementById('horario').value,
            local: document.getElementById('local').value,
            quantidade_vagas: Number(document.getElementById('quantidade_vagas').value),
        };
        try {
            await request(`${apiBase}/eventos`, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            setMessage(eventoFormMessage, 'Evento criado.', 'success');
            event.target.reset();
            loadEventos();
        } catch (err) {
            setMessage(eventoFormMessage, err.message, 'error');
        }
    });

    document.getElementById('participantes-evento-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const eventoId = eventoBuscaSelect.value;

        if (!eventoId) {
            setMessage(participantesMessage, 'Selecione um evento para ver os inscritos.', 'error');
            participantesList.innerHTML = '';
            return;
        }

        try {
            const data = await request(`${apiBase}/eventos/${eventoId}/participantes`);
            const items = data?.data || [];
            participantesList.innerHTML = items.length ?
                `<ul>${items.map(p => `<li>${p.nome} — ${p.email}</li>`).join('')}</ul>` :
                '<p>Nenhum participante inscrito.</p>';
            setMessage(participantesMessage, '');
        } catch (err) {
            setMessage(participantesMessage, err.message, 'error');
            participantesList.innerHTML = '';
        }
    });

    loadEventos();
</script>
@endsection