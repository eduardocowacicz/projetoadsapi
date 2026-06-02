@extends('layouts.app')

@section('content')
    <h1>Participantes</h1>

    <div class="grid two">
        <div class="card">
            <h2>Novo participante</h2>
            <form id="participante-form">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input id="nome" name="nome" type="text" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input id="telefone" name="telefone" type="text">
                </div>
                <button type="submit">Salvar</button>
                <div id="participante-form-message" class="message"></div>
            </form>
        </div>
        <div class="card">
            <h2>Acoes rapidas</h2>
            <p>Use esta tela para cadastrar participantes e visualizar a lista.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Lista de participantes</h2>
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
        const participantesTableBody = document.querySelector('#participantes-table tbody');
        const participantesMessage = document.getElementById('participantes-message');
        const participanteFormMessage = document.getElementById('participante-form-message');

        const setMessage = (el, text, type) => {
            el.textContent = text || '';
            el.className = type ? `message ${type}` : 'message';
        };

        const request = async (url, options = {}) => {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                ...options
            });

            if (response.status === 204) {
                return null;
            }

            const data = await response.json().catch(() => null);

            if (!response.ok) {
                throw new Error(data?.message || 'Erro na requisicao');
            }

            return data;
        };

        const loadParticipantes = async () => {
            try {
                setMessage(participantesMessage, '');
                const data = await request(`${apiBase}/participantes`);
                const items = data?.data || [];
                participantesTableBody.innerHTML = items.map((participante) => `
                    <tr>
                        <td>${participante.id}</td>
                        <td>${participante.nome}</td>
                        <td>${participante.email}</td>
                        <td>${participante.telefone || ''}</td>
                        <td><button class="danger" data-delete="${participante.id}">Excluir</button></td>
                    </tr>
                `).join('');
            } catch (error) {
                setMessage(participantesMessage, error.message, 'error');
            }
        };

        document.getElementById('participantes-refresh').addEventListener('click', loadParticipantes);

        participantesTableBody.addEventListener('click', async (event) => {
            const target = event.target;
            if (target.dataset.delete) {
                try {
                    await request(`${apiBase}/participantes/${target.dataset.delete}`, { method: 'DELETE' });
                    setMessage(participantesMessage, 'Participante excluido.', 'success');
                    loadParticipantes();
                } catch (error) {
                    setMessage(participantesMessage, error.message, 'error');
                }
            }
        });

        document.getElementById('participante-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = {
                nome: document.getElementById('nome').value,
                email: document.getElementById('email').value,
                telefone: document.getElementById('telefone').value || null
            };

            try {
                await request(`${apiBase}/participantes`, {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                setMessage(participanteFormMessage, 'Participante criado.', 'success');
                event.target.reset();
                loadParticipantes();
            } catch (error) {
                setMessage(participanteFormMessage, error.message, 'error');
            }
        });

        loadParticipantes();
    </script>
@endsection
