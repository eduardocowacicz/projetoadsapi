@extends('layouts.app')

@section('content')
<h1>Inscricoes</h1>

<div class="grid two">
    <div class="card">
        <h2>Realizar inscricao</h2>
        <form id="inscricao-form">
            <div class="form-group">
                <label for="evento_id">Evento com vagas</label>
                <select id="evento_id" name="evento_id" required>
                    <option value="">Carregando eventos...</option>
                </select>
            </div>
            <div class="form-group">
                <label for="participante_id">Participante cadastrado</label>
                <select id="participante_id" name="participante_id" required>
                    <option value="">Carregando participantes...</option>
                </select>
            </div>
            <p id="inscricao-form-note">Selecione um evento e um participante para concluir a inscricao.</p>
            <button type="submit">Inscrever</button>
            <div id="inscricao-form-message" class="message"></div>
        </form>
    </div>
    <div class="card">
        <h2>Informacoes</h2>
        <p>Esta tela nao cadastra novos usuarios. Ela apenas vincula um <strong>participante ja cadastrado</strong> a um <strong>evento com vagas</strong>.</p>
        <p>Se nao aparecer nenhum evento ou participante, primeiro cadastre os dados nas telas de <strong>Eventos</strong> e <strong>Participantes</strong>.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Lista de inscricoes</h2>
        <button id="inscricoes-refresh" class="secondary" type="button">Atualizar</button>
    </div>
    <div id="inscricoes-message" class="message"></div>
    <table class="table" id="inscricoes-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Evento</th>
                <th>Participante</th>
                <th>Status</th>
                <th>Data inscricao</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    const apiBase = '/api';
    const csrfToken = document.querySelector('#csrf-token')?.content;
    const eventoSelect = document.getElementById('evento_id');
    const participanteSelect = document.getElementById('participante_id');
    const inscricoesTableBody = document.querySelector('#inscricoes-table tbody');
    const inscricoesMessage = document.getElementById('inscricoes-message');
    const inscricaoFormMessage = document.getElementById('inscricao-form-message');
    const inscricaoFormNote = document.getElementById('inscricao-form-note');

    const statusBadge = (status) => {
        const cls = {
            ativa: 'badge-success',
            cancelada: 'badge-danger'
        };
        return `<span class="badge ${cls[status] || ''}">${status}</span>`;
    };

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

    const formatDate = (iso) => {
        if (!iso) return '—';
        return new window.Date(iso).toLocaleString('pt-BR', {
            dateStyle: 'short',
            timeStyle: 'short'
        });
    };

    const setSelectOptions = (selectEl, items, placeholder, mapLabel) => {
        const options = [`<option value="">${placeholder}</option>`];

        for (const item of items) {
            options.push(`<option value="${item.id}">${mapLabel(item)}</option>`);
        }

        selectEl.innerHTML = options.join('');
        selectEl.disabled = items.length === 0;
    };

    const loadFormOptions = async () => {
        try {
            const [eventosData, participantesData] = await Promise.all([
                request(`${apiBase}/eventos/vagas-disponiveis`),
                request(`${apiBase}/participantes?per_page=100`),
            ]);

            const eventos = eventosData?.data || [];
            const participantes = participantesData?.data || [];

            setSelectOptions(
                eventoSelect,
                eventos,
                eventos.length ? 'Selecione um evento' : 'Nenhum evento com vagas disponivel',
                (evento) => `${evento.titulo} - ${evento.data || 'sem data'} (${evento.vagas_disponiveis}/${evento.quantidade_vagas} vagas)`
            );

            setSelectOptions(
                participanteSelect,
                participantes,
                participantes.length ? 'Selecione um participante' : 'Nenhum participante cadastrado',
                (participante) => `${participante.nome} - ${participante.email}`
            );

            inscricaoFormNote.textContent = (eventos.length === 0 || participantes.length === 0)
                ? 'Para realizar a inscricao, cadastre ao menos um evento com vagas e um participante.'
                : 'Selecione um evento e um participante para concluir a inscricao.';
        } catch (err) {
            eventoSelect.innerHTML = '<option value="">Erro ao carregar eventos</option>';
            participanteSelect.innerHTML = '<option value="">Erro ao carregar participantes</option>';
            eventoSelect.disabled = true;
            participanteSelect.disabled = true;
            inscricaoFormNote.textContent = 'Nao foi possivel carregar os dados necessarios para a inscricao.';
            setMessage(inscricaoFormMessage, err.message, 'error');
        }
    };

    const loadInscricoes = async () => {
        try {
            setMessage(inscricoesMessage, '');
            const data = await request(`${apiBase}/inscricoes?per_page=100`);
            const items = data?.data || [];

            if (items.length === 0) {
                inscricoesTableBody.innerHTML = `
                    <tr>
                        <td colspan="6">Nenhuma inscricao encontrada.</td>
                    </tr>
                `;
                return;
            }

            inscricoesTableBody.innerHTML = items.map(i => `
                    <tr>
                        <td>${i.id}</td>
                        <td>${i.evento ? `#${i.evento.id} — ${i.evento.titulo}` : i.evento_id}</td>
                        <td>${i.participante ? `${i.participante.nome} (${i.participante.email})` : i.participante_id}</td>
                        <td>${statusBadge(i.status)}</td>
                        <td>${formatDate(i.data_inscricao)}</td>
                        <td>
                            ${i.status === 'ativa'
                                ? `<button class="danger btn-sm" data-cancel="${i.id}">Cancelar</button>`
                                : '—'}
                        </td>
                    </tr>
                `).join('');
        } catch (err) {
            setMessage(inscricoesMessage, err.message, 'error');
        }
    };

    inscricoesTableBody.addEventListener('click', async (event) => {
        const el = event.target;
        if (el.dataset.cancel) {
            if (!confirm('Cancelar esta inscricao?')) return;
            try {
                await request(`${apiBase}/inscricoes/${el.dataset.cancel}`, {
                    method: 'DELETE'
                });
                setMessage(inscricoesMessage, 'Inscricao cancelada.', 'success');
                await Promise.all([loadInscricoes(), loadFormOptions()]);
            } catch (err) {
                setMessage(inscricoesMessage, err.message, 'error');
            }
        }
    });

    document.getElementById('inscricoes-refresh').addEventListener('click', loadInscricoes);

    document.getElementById('inscricao-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const payload = {
            evento_id: Number(eventoSelect.value),
            participante_id: Number(participanteSelect.value),
        };

        if (!payload.evento_id || !payload.participante_id) {
            setMessage(inscricaoFormMessage, 'Selecione um evento e um participante.', 'error');
            return;
        }

        try {
            await request(`${apiBase}/inscricoes`, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            setMessage(inscricaoFormMessage, 'Inscricao criada.', 'success');
            event.target.reset();
            await Promise.all([loadInscricoes(), loadFormOptions()]);
        } catch (err) {
            setMessage(inscricaoFormMessage, err.message, 'error');
        }
    });

    Promise.all([loadInscricoes(), loadFormOptions()]);
</script>
@endsection