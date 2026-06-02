@extends('layouts.app')

@section('content')
    <h1>Eventos</h1>

    <div class="grid two">
        <div class="card">
            <h2>Novo evento</h2>
            <form id="evento-form">
                <div class="form-group">
                    <label for="titulo">Titulo</label>
                    <input id="titulo" name="titulo" type="text" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descricao</label>
                    <textarea id="descricao" name="descricao" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="data">Data</label>
                    <input id="data" name="data" type="date" required>
                </div>
                <div class="form-group">
                    <label for="horario">Horario</label>
                    <input id="horario" name="horario" type="time" required>
                </div>
                <div class="form-group">
                    <label for="local">Local</label>
                    <input id="local" name="local" type="text" required>
                </div>
                <div class="form-group">
                    <label for="quantidade_vagas">Quantidade de vagas</label>
                    <input id="quantidade_vagas" name="quantidade_vagas" type="number" min="1" required>
                </div>
                <button type="submit">Salvar</button>
                <div id="evento-form-message" class="message"></div>
            </form>
        </div>
        <div class="card">
            <h2>Participantes do evento</h2>
            <form id="participantes-evento-form">
                <div class="form-group">
                    <label for="evento_id_busca">ID do evento</label>
                    <input id="evento_id_busca" type="number" min="1" required>
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
        const eventosTableBody = document.querySelector('#eventos-table tbody');
        const eventosMessage = document.getElementById('eventos-message');
        const eventoFormMessage = document.getElementById('evento-form-message');
        const participantesMessage = document.getElementById('participantes-evento-message');
        const participantesList = document.getElementById('participantes-evento-list');

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

        const loadEventos = async () => {
            try {
                setMessage(eventosMessage, '');
                const data = await request(`${apiBase}/eventos`);
                const items = data?.data || [];
                eventosTableBody.innerHTML = items.map((evento) => `
                    <tr>
                        <td>${evento.id}</td>
                        <td>${evento.titulo}</td>
                        <td>${evento.data || ''}</td>
                        <td>${evento.horario || ''}</td>
                        <td>${evento.local || ''}</td>
                        <td>${evento.vagas_disponiveis}/${evento.quantidade_vagas}</td>
                        <td>${evento.status}</td>
                        <td><button class="danger" data-delete="${evento.id}">Excluir</button></td>
                    </tr>
                `).join('');
            } catch (error) {
                setMessage(eventosMessage, error.message, 'error');
            }
        };

        document.getElementById('eventos-refresh').addEventListener('click', loadEventos);

        eventosTableBody.addEventListener('click', async (event) => {
            const target = event.target;
            if (target.dataset.delete) {
                try {
                    await request(`${apiBase}/eventos/${target.dataset.delete}`, { method: 'DELETE' });
                    setMessage(eventosMessage, 'Evento excluido.', 'success');
                    loadEventos();
                } catch (error) {
                    setMessage(eventosMessage, error.message, 'error');
                }
            }
        });

        document.getElementById('evento-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = {
                titulo: document.getElementById('titulo').value,
                descricao: document.getElementById('descricao').value || null,
                data: document.getElementById('data').value,
                horario: document.getElementById('horario').value,
                local: document.getElementById('local').value,
                quantidade_vagas: Number(document.getElementById('quantidade_vagas').value)
            };

            try {
                await request(`${apiBase}/eventos`, {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                setMessage(eventoFormMessage, 'Evento criado.', 'success');
                event.target.reset();
                loadEventos();
            } catch (error) {
                setMessage(eventoFormMessage, error.message, 'error');
            }
        });

        document.getElementById('participantes-evento-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const eventoId = document.getElementById('evento_id_busca').value;
            try {
                const data = await request(`${apiBase}/eventos/${eventoId}/participantes`);
                const items = data?.data || [];
                participantesList.innerHTML = items.length
                    ? `<ul>${items.map(item => `<li>${item.nome} - ${item.email}</li>`).join('')}</ul>`
                    : '<p>Nenhum participante encontrado.</p>';
                setMessage(participantesMessage, '');
            } catch (error) {
                setMessage(participantesMessage, error.message, 'error');
                participantesList.innerHTML = '';
            }
        });

        loadEventos();
    </script>
@endsection
