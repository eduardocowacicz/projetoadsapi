@extends('layouts.app')

@section('content')
<h1>Inscricoes</h1>

<div class="grid two">
    <div class="card">
        <h2>Nova inscricao</h2>
        <form id="inscricao-form">
            <div class="form-group">
                <label for="evento_id">ID do evento</label>
                <input id="evento_id" name="evento_id" type="number" min="1" required>
            </div>
            <div class="form-group">
                <label for="participante_id">ID do participante</label>
                <input id="participante_id" name="participante_id" type="number" min="1" required>
            </div>
            <button type="submit">Inscrever</button>
            <div id="inscricao-form-message" class="message"></div>
        </form>
    </div>
    <div class="card">
        <h2>Informacoes</h2>
        <p>Use os IDs das tabelas de <strong>Eventos</strong> e <strong>Participantes</strong> para realizar inscricoes. Para cancelar, clique em <strong>Cancelar</strong> diretamente na tabela abaixo.</p>
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
    const inscricoesTableBody = document.querySelector('#inscricoes-table tbody');
    const inscricoesMessage = document.getElementById('inscricoes-message');
    const inscricaoFormMessage = document.getElementById('inscricao-form-message');

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
        return new Date(iso).toLocaleString('pt-BR', {
            dateStyle: 'short',
            timeStyle: 'short'
        });
    };

    const loadInscricoes = async () => {
        try {
            setMessage(inscricoesMessage, '');
            const data = await request(`${apiBase}/inscricoes?per_page=100`);
            inscricoesTableBody.innerHTML = (data?.data || []).map(i => `
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
                loadInscricoes();
            } catch (err) {
                setMessage(inscricoesMessage, err.message, 'error');
            }
        }
    });

    document.getElementById('inscricoes-refresh').addEventListener('click', loadInscricoes);

    document.getElementById('inscricao-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const payload = {
            evento_id: Number(document.getElementById('evento_id').value),
            participante_id: Number(document.getElementById('participante_id').value),
        };
        try {
            await request(`${apiBase}/inscricoes`, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            setMessage(inscricaoFormMessage, 'Inscricao criada.', 'success');
            event.target.reset();
            loadInscricoes();
        } catch (err) {
            setMessage(inscricaoFormMessage, err.message, 'error');
        }
    });

    loadInscricoes();
</script>
@endsection