@extends('layouts.app')

@section('content')
<h1>Cadastro de participantes</h1>

<div id="participante-modal" class="modal-backdrop" style="display:none">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="margin:0">Editar Participante</h3>
            <button type="button" id="participante-modal-close" class="secondary">✕</button>
        </div>
        <form id="participante-edit-form" style="margin-top:16px">
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label for="edit-nome">Nome</label>
                <input id="edit-nome" type="text" required>
            </div>
            <div class="form-group">
                <label for="edit-email">Email</label>
                <input id="edit-email" type="email" required>
            </div>
            <div class="form-group">
                <label for="edit-telefone">Telefone</label>
                <input id="edit-telefone" type="text">
            </div>
            <div class="modal-footer">
                <div id="participante-edit-message" class="message"></div>
                <button type="submit">Salvar alteracoes</button>
            </div>
        </form>
    </div>
</div>

<div class="grid two">
    <div class="card">
        <h2>Novo participante</h2>
        <form id="participante-form">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input id="nome" type="text" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input id="telefone" type="text">
            </div>
            <button type="submit">Salvar</button>
            <div id="participante-form-message" class="message"></div>
        </form>
    </div>
    <div class="card">
        <h2>Como funciona</h2>
        <p>Cadastre aqui as pessoas que poderao ser inscritas nos eventos. A inscricao em si e feita depois, na tela de <strong>Inscricoes</strong>.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Participantes cadastrados</h2>
        <button id="participantes-refresh" class="secondary" type="button">Atualizar</button>
    </div>
    <div id="participantes-message" class="message"></div>
    <table class="table" id="participantes-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    const apiBase = '/api';
    const csrfToken = document.querySelector('#csrf-token')?.content;
    const participantesTableBody = document.querySelector('#participantes-table tbody');
    const participantesMessage = document.getElementById('participantes-message');
    const participanteFormMessage = document.getElementById('participante-form-message');
    const participanteModal = document.getElementById('participante-modal');
    const participanteEditMessage = document.getElementById('participante-edit-message');

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

    const loadParticipantes = async () => {
        try {
            setMessage(participantesMessage, '');
            const data = await request(`${apiBase}/participantes?per_page=100`);
            participantesTableBody.innerHTML = (data?.data || []).map(p => `
                    <tr>
                        <td>${p.id}</td>
                        <td>${p.nome}</td>
                        <td>${p.email}</td>
                        <td>${p.telefone || '—'}</td>
                        <td class="td-actions">
                            <button class="secondary btn-sm" data-edit='${JSON.stringify(p).replace(/'/g, "&#39;")}'>Editar</button>
                            <button class="danger btn-sm" data-delete="${p.id}">Excluir</button>
                        </td>
                    </tr>
                `).join('');
        } catch (err) {
            setMessage(participantesMessage, err.message, 'error');
        }
    };

    participantesTableBody.addEventListener('click', async (event) => {
        const el = event.target;
        if (el.dataset.edit) {
            const p = JSON.parse(el.dataset.edit);
            document.getElementById('edit-id').value = p.id;
            document.getElementById('edit-nome').value = p.nome;
            document.getElementById('edit-email').value = p.email;
            document.getElementById('edit-telefone').value = p.telefone || '';
            setMessage(participanteEditMessage, '');
            participanteModal.style.display = 'flex';
        }
        if (el.dataset.delete) {
            if (!confirm('Excluir este participante?')) return;
            try {
                await request(`${apiBase}/participantes/${el.dataset.delete}`, {
                    method: 'DELETE'
                });
                setMessage(participantesMessage, 'Participante excluido.', 'success');
                loadParticipantes();
            } catch (err) {
                setMessage(participantesMessage, err.message, 'error');
            }
        }
    });

    document.getElementById('participante-modal-close').addEventListener('click', () => participanteModal.style.display = 'none');
    participanteModal.addEventListener('click', e => {
        if (e.target === participanteModal) participanteModal.style.display = 'none';
    });

    document.getElementById('participante-edit-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const id = document.getElementById('edit-id').value;
        const payload = {
            nome: document.getElementById('edit-nome').value,
            email: document.getElementById('edit-email').value,
            telefone: document.getElementById('edit-telefone').value || null,
        };
        try {
            await request(`${apiBase}/participantes/${id}`, {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
            setMessage(participanteEditMessage, 'Salvo com sucesso!', 'success');
            loadParticipantes();
            setTimeout(() => participanteModal.style.display = 'none', 900);
        } catch (err) {
            setMessage(participanteEditMessage, err.message, 'error');
        }
    });

    document.getElementById('participantes-refresh').addEventListener('click', loadParticipantes);

    document.getElementById('participante-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const payload = {
            nome: document.getElementById('nome').value,
            email: document.getElementById('email').value,
            telefone: document.getElementById('telefone').value || null,
        };
        try {
            await request(`${apiBase}/participantes`, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            setMessage(participanteFormMessage, 'Participante criado.', 'success');
            event.target.reset();
            loadParticipantes();
        } catch (err) {
            setMessage(participanteFormMessage, err.message, 'error');
        }
    });

    loadParticipantes();
</script>
@endsection