/**
 * JavaScript del Chatbot
 * Maneja la interacción del usuario y comunicación con el backend
 */

// Elementos del DOM
const chatForm = document.getElementById('chatForm');
const userInput = document.getElementById('userInput');
const sendButton = document.getElementById('sendButton');
const chatMessages = document.getElementById('chatMessages');
const typingIndicator = document.getElementById('typingIndicator');

// Estado
let isWaitingResponse = false;

/**
 * Inicialización
 */
document.addEventListener('DOMContentLoaded', () => {
    // Auto-resize del textarea
    userInput.addEventListener('input', autoResizeTextarea);

    // Manejar envío del formulario
    chatForm.addEventListener('submit', handleSubmit);

    // Manejar Enter (sin Shift)
    userInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // Focus inicial
    userInput.focus();
});

/**
 * Auto-resize del textarea
 */
function autoResizeTextarea() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
}

/**
 * Maneja el envío del formulario
 */
async function handleSubmit(e) {
    e.preventDefault();

    const mensaje = userInput.value.trim();

    // Validaciones
    if (!mensaje) return;
    if (isWaitingResponse) return;
    if (mensaje.length > 500) {
        mostrarError('El mensaje es demasiado largo (máximo 500 caracteres)');
        return;
    }

    // Agregar mensaje del usuario
    agregarMensaje(mensaje, 'user');

    // Limpiar input
    userInput.value = '';
    userInput.style.height = 'auto';

    // Deshabilitar input mientras se espera respuesta
    isWaitingResponse = true;
    sendButton.disabled = true;
    userInput.disabled = true;

    // Mostrar indicador de escritura
    mostrarTypingIndicator();

    try {
        // Enviar al backend
        const respuesta = await enviarMensaje(mensaje);

        // Ocultar indicador
        ocultarTypingIndicator();

        // Mostrar respuesta del bot
        if (respuesta.respuesta) {
            agregarMensaje(respuesta.respuesta, 'bot', respuesta.claves_validas, respuesta.is_main_menu);
        } else if (respuesta.error) {
            mostrarError(respuesta.error);
        }

    } catch (error) {
        ocultarTypingIndicator();
        mostrarError('Error de conexión. Por favor, verifica tu conexión a internet e intenta nuevamente.');
        console.error('Error:', error);
    } finally {
        // Rehabilitar input
        isWaitingResponse = false;
        sendButton.disabled = false;
        userInput.disabled = false;
        userInput.focus();
    }
}

/**
 * Envía el mensaje al backend
 */
async function enviarMensaje(mensaje) {
    const response = await fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ mensaje })
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
}

/**
 * Agrega un mensaje al chat
 */
function agregarMensaje(texto, tipo, clavesValidas = null, isMainMenu = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${tipo}-message`;

    if (tipo === 'bot') {
        const avatarImg = document.createElement('img');
        avatarImg.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImJnIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjZmY5YTllIi8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjZmVjZmVmIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9InVybCgjYmcpIi8+PGc+PGFuaW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIiB0eXBlPSJ0cmFuc2xhdGUiIHZhbHVlcz0iMCwwOyAwLDE7IDAsMCIgZHVyPSIycyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48cGF0aCBkPSJNMjUgNjAgUTIwIDIwIDUwIDE1IFE4MCAyMCA3NSA2MCBMODAgODUgUTUwIDk1IDIwIDg1IFoiIGZpbGw9IiM0YTJlMWIiLz48cmVjdCB4PSI0MiIgeT0iNjUiIHdpZHRoPSIxNiIgaGVpZ2h0PSIyMCIgcng9IjUiIGZpbGw9IiNmZmNjYTciLz48cGF0aCBkPSJNMjAgMTAwIFE1MCA2NSA4MCAxMDAgWiIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik00MiA4MCBMNTAgODggTDU4IDgwIFoiIGZpbGw9IiNmMGYwZjAiLz48cmVjdCB4PSIzMCIgeT0iMzAiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MiIgcng9IjIwIiBmaWxsPSIjZmZlMGM4Ii8+PHBhdGggZD0iTTMwIDQ1IFE0MCAyNSA3MCAzNSBRNTAgMjAgMzAgNDUgWiIgZmlsbD0iIzVjM2EyMSIvPjxnPjxjaXJjbGUgY3g9IjQwIiBjeT0iNDgiIHI9IjMuNSIgZmlsbD0iIzMzMyI+PGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iciIgdmFsdWVzPSIzLjU7IDMuNTsgMDsgMy41OyAzLjUiIGtleVRpbWVzPSIwOyAwLjk7IDAuOTU7IDAuOTg7IDEiIGR1cj0iNHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+PC9jaXJjbGU+PGNpcmNsZSBjeD0iNjAiIGN5PSI0OCIgcj0iMy41IiBmaWxsPSIjMzMzIj48YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJyIiB2YWx1ZXM9IjMuNTsgMy41OyAwOyAzLjU7IDMuNSIga2V5VGltZXM9IjA7IDAuOTsgMC45NTsgMC45ODsgMSIgZHVyPSI0cyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48L2NpcmNsZT48L2c+PHBhdGggZD0iTTM2IDQ2IFE0MCA0MyA0NCA0NiIgc3Ryb2tlPSIjMzMzIiBzdHJva2Utd2lkdGg9IjEuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PHBhdGggZD0iTTU2IDQ2IFE2MCA0MyA2NCA0NiIgc3Ryb2tlPSIjMzMzIiBzdHJva2Utd2lkdGg9IjEuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PGNpcmNsZSBjeD0iMzUiIGN5PSI1NCIgcj0iNCIgZmlsbD0iI2ZmOTk5OSIgb3BhY2l0eT0iMC41Ii8+PGNpcmNsZSBjeD0iNjUiIGN5PSI1NCIgcj0iNCIgZmlsbD0iI2ZmOTk5OSIgb3BhY2l0eT0iMC41Ii8+PHBhdGggZD0iTTQ1IDU4IFE1MCA2NCA1NSA1OCIgc3Ryb2tlPSIjZDY1YTVhIiBzdHJva2Utd2lkdGg9IjIuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9nPjwvc3ZnPg==';
        avatarImg.className = 'message-avatar bot-avatar-img animated-avatar';
        avatarImg.alt = 'Bot Avatar';
        messageDiv.appendChild(avatarImg);
    }

    const wrapperDiv = document.createElement('div');
    wrapperDiv.className = 'message-wrapper';

    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';

    const messageNode = document.createElement('div');
    messageNode.className = 'formatted-message';
    
    if (isMainMenu && tipo === 'bot') {
        messageNode.innerHTML = `
            <p>¡Hola! Soy tu asistente de Potencial Humano. Elige un tema o escribe tu consulta:</p>
            <div class="welcome-menu grid-menu" style="margin-top: 10px;">
                <button type="button" class="grid-btn" onclick="sendOption('1. CONTROL DE ASISTENCIA')"><i class="fas fa-clock"></i><span>Control de Asistencia</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('2. LICENCIAS')"><i class="fas fa-file-medical"></i><span>Licencias</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('3. BIENESTAR DE PERSONAL')"><i class="fas fa-heartbeat"></i><span>Bienestar de Personal</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('4. CREDENCIAL Y FOTOCHECK')"><i class="fas fa-id-badge"></i><span>Credencial y Fotocheck</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO')"><i class="fas fa-file-signature"></i><span>Constancias y Certificados</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('6. VACACIONES')"><i class="fas fa-umbrella-beach"></i><span>Vacaciones</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('7. DECLARACIÓN JURADA DE INGRESOS Y DE BIENES Y RENTAS')"><i class="fas fa-file-invoice-dollar"></i><span>DJ de Ingresos y Bienes</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('8. DECLARACIÓN JURADA DE INTERESES')"><i class="fas fa-balance-scale"></i><span>DJ de Intereses</span></button>
                <button type="button" class="grid-btn" onclick="sendOption('9. BOLETAS DE PAGO')"><i class="fas fa-receipt"></i><span>Boletas de Pago</span></button>
            </div>`;
    } else {
        messageNode.innerHTML = formatMessage(texto, tipo, clavesValidas);
    }
    
    contentDiv.appendChild(messageNode);

    const timeDiv = document.createElement('div');
    timeDiv.className = 'message-time';
    timeDiv.textContent = obtenerHoraActual();

    wrapperDiv.appendChild(contentDiv);
    wrapperDiv.appendChild(timeDiv);

    messageDiv.appendChild(wrapperDiv);
    chatMessages.appendChild(messageDiv);

    // Scroll al final
    scrollToBottom();
}

/**
 * Muestra un mensaje de error
 */
function mostrarError(mensaje) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message bot-message';

    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content error-message';

    const p = document.createElement('p');
    p.textContent = '⚠️ ' + mensaje;

    contentDiv.appendChild(p);
    messageDiv.appendChild(contentDiv);

    chatMessages.appendChild(messageDiv);
    scrollToBottom();
}

/**
 * Muestra el indicador de escritura
 */
function mostrarTypingIndicator() {
    typingIndicator.style.display = 'flex';
    scrollToBottom();
}

/**
 * Oculta el indicador de escritura
 */
function ocultarTypingIndicator() {
    typingIndicator.style.display = 'none';
}

/**
 * Scroll automático al final
 */
function scrollToBottom() {
    setTimeout(() => {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 100);
}

/**
 * Obtiene la hora actual formateada
 */
function obtenerHoraActual() {
    const ahora = new Date();
    const horas = ahora.getHours().toString().padStart(2, '0');
    const minutos = ahora.getMinutes().toString().padStart(2, '0');
    return `${horas}:${minutos}`;
}

/**
 * Formatea el texto del mensaje para soportar Markdown básico y crear botones interactivos
 */
function formatMessage(texto, tipo, clavesValidas = null) {
    // Si es del usuario, solo escapar y respetar saltos de línea
    let html = texto.replace(/</g, "&lt;").replace(/>/g, "&gt;");

    // Convertir saltos de línea
    html = html.replace(/\n/g, '<br>');

    if (tipo !== 'bot') return html;

    // Negritas Markdown (**)
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    // Cursivas Markdown (*)
    html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');

    if (clavesValidas && Array.isArray(clavesValidas) && clavesValidas.length > 0) {
        // MODO INTELIGENTE: Si tenemos las claves válidas desde el backend, 
        // convertimos EXACTAMENTE esas líneas en botones.
        const lines = html.split('<br>');
        for (let i = 0; i < lines.length; i++) {
            let lineText = lines[i].replace(/<[^>]+>/g, '').trim();
            // lineText puede contener los emojis. El array_keys de PHP también.
            if (lineText && clavesValidas.includes(lineText)) {
                let safeText = lineText.replace(/'/g, "\\'");
                let btnClass = 'menu-btn sub-btn';
                
                // Las opciones del menú principal (1 al 12 en mayúsculas) son main-btn
                if (/^(?:1[0-2]|[1-9])\.\s+[A-Z]/.test(lineText)) {
                    btnClass = 'menu-btn main-btn';
                }
                
                if (lineText.includes('Volver')) {
                    lines[i] = `<button type="button" class="menu-btn sub-btn" style="background-color: #f0f4f8; color: #0056b3; font-weight: 500;" onclick="sendOption('${safeText}')">${lineText}</button>`;
                } else {
                    lines[i] = `<button type="button" class="${btnClass}" onclick="sendOption('${safeText}')">${lineText}</button>`;
                }
            }
        }
        html = lines.join('<br>');
    } else {
        // FALLBACK AL REGEX ANTIGUO si no hay claves_validas
        // Convertir las opciones principales de 1 al 12 en botones grandes
        const mainRegex = /(?:<br>|^)\s*(1[0-2]|[1-9])\.\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=<br>|$)/g;
        html = html.replace(mainRegex, function (match, id, text) {
            if (text.length > 70) return match;
            const fullOption = `${id}. ${text.trim()}`;
            return `<br><button type="button" class="menu-btn main-btn" onclick="sendOption('${fullOption}')">${fullOption}</button>`;
        });

        // Convertir subopciones a. b. c. o i. ii. en botones pequeños
        const subRegex = /(?:<br>|^)\s*([a-z]\.|[i,v]{1,3}\.)\s+([^<]+?)(?=<br>|$)/g;
        html = html.replace(subRegex, function (match, id, text) {
            if (text.length > 75 || text.includes(':')) return match;
            const fullOption = `${id.trim()} ${text.trim()}`;
            return `<br><button type="button" class="menu-btn sub-btn" onclick="sendOption('${fullOption}')">${fullOption}</button>`;
        });

        // Convertir botones de acción (volver atrás, menú principal)
        const actionRegex = /(?:<br>|^)\s*(🔙[^<]*|🏠[^<]*)(?=<br>|$)/g;
        html = html.replace(actionRegex, function (match, text) {
            let safeText = text.trim().replace(/'/g, "\\'").replace(/\n|\r/g, "");
            return `<br><button type="button" class="menu-btn sub-btn" style="background-color: #f0f4f8; color: #0056b3; font-weight: 500;" onclick="sendOption('${safeText}')">${text.trim()}</button>`;
        });
    }

    // Parsear Enlaces Markdown [texto](url)
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" class="chat-link" rel="noopener noreferrer">📄 $1</a>');

    return html;
}

/**
 * Función global para enviar opciones al hacer clic en un botón del chat
 */
window.sendOption = function (texto) {
    const input = document.getElementById('userInput');
    input.value = texto;

    // Habilitar submit si estaba bloqueado
    const botonEnviar = document.getElementById('sendButton');
    botonEnviar.disabled = false;

    // Disparar click en el botón de enviar para invocar el submit
    botonEnviar.click();
};
