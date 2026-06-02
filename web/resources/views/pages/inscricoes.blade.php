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
            <h2>Cancelar inscricao</h2>
            <form id="cancelar-inscricao-form">
                <div class="form-group">
                    <label for="inscricao_id">ID da inscricao</label>
                    <input id="inscricao_id" name="inscricao_id" type="number" min="1" required>
                </div>
                <button type="submit" class="danger">Cancelar</button>
                <div id="cancelar-inscricao-message" class="message"></div>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Retorno da operacao</h2>
        <pre id="inscricao-output"></pre>
    </div>

    <script>
        const apiBase = '/api';
        const inscricaoOutput = document.getElementById('inscricao-output');
        const inscricaoMessage = document.getElementById('inscricao-form-message');
        const cancelarMessage = document.getElementById('cancelar-inscricao-message');

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

        document.getElementById('inscricao-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = {
                evento_id: Number(document.getElementById('evento_id').value),
                participante_id: Number(document.getElementById('participante_id').value)
            };

            try {
                const data = await request(`${apiBase}/inscricoes`, {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                setMessage(inscricaoMessage, 'Inscricao criada.', 'success');
                inscricaoOutput.textContent = JSON.stringify(data, null, 2);
                event.target.reset();
            } catch (error) {
                setMessage(inscricaoMessage, error.message, 'error');
                inscricaoOutput.textContent = '';
            }
        });

        document.getElementById('cancelar-inscricao-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const inscricaoId = document.getElementById('inscricao_id').value;
            try {
                const data = await request(`${apiBase}/inscricoes/${inscricaoId}`, {
                    method: 'DELETE'
                });
                setMessage(cancelarMessage, 'Inscricao cancelada.', 'success');
                inscricaoOutput.textContent = JSON.stringify(data, null, 2);
                event.target.reset();
            } catch (error) {
                setMessage(cancelarMessage, error.message, 'error');
                inscricaoOutput.textContent = '';
            }
        });
    </script>
@endsection
