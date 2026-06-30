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
const micButton = document.getElementById('micButton');

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
    
    // Inicializar voces para Text-to-Speech
    if ('speechSynthesis' in window) {
        window.speechSynthesis.getVoices();
    }
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
    
    // Detener audio anterior si el bot estaba hablando
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }
    
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
            agregarMensaje(respuesta.respuesta, 'bot');
            hablarTexto(respuesta.respuesta); // <-- Reproducir voz
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
function agregarMensaje(texto, tipo) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${tipo}-message`;
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    
    const p = document.createElement('p');
    p.textContent = texto;
    
    contentDiv.appendChild(p);
    
    const timeDiv = document.createElement('div');
    timeDiv.className = 'message-time';
    timeDiv.textContent = obtenerHoraActual();
    
    messageDiv.appendChild(contentDiv);
    messageDiv.appendChild(timeDiv);
    
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
 * =========================================================
 * INTEGRACIÓN DE VOZ (Web Speech API)
 * =========================================================
 */

// 1. VOZ A TEXTO (Speech Recognition)
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition = null;
let isListening = false;

if (SpeechRecognition) {
    recognition = new SpeechRecognition();
    recognition.continuous = false;
    recognition.lang = 'es-PE'; // Español de Perú
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.onstart = function() {
        isListening = true;
        if(micButton) micButton.classList.add('listening');
        userInput.placeholder = "Escuchando...";
    };

    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript;
        userInput.value = transcript;
        
        // Auto-enviar el formulario automáticamente después de hablar
        setTimeout(() => chatForm.dispatchEvent(new Event('submit')), 300);
    };

    recognition.onerror = function(event) {
        console.error("Error reconociendo voz: ", event.error);
        stopListening();
    };

    recognition.onend = function() {
        stopListening();
    };
} else {
    // El navegador no soporta entrada de voz
    if (micButton) {
        micButton.style.display = 'none';
    }
}

function stopListening() {
    isListening = false;
    if(micButton) micButton.classList.remove('listening');
    userInput.placeholder = "Escribe tu mensaje aquí...";
}

// Evento de clic en el micrófono
if (micButton) {
    micButton.addEventListener('click', () => {
        if (!recognition) return;
        
        if (isListening) {
            recognition.stop();
        } else {
            recognition.start();
        }
    });
}

// 2. TEXTO A VOZ (Speech Synthesis)
function hablarTexto(texto) {
    if (!('speechSynthesis' in window)) return;
    
    // Cancelar audios anteriores pendientes
    window.speechSynthesis.cancel();
    
    // Limpiar asteriscos (markdown) para que el bot no los lea ("asterisco")
    let textoLimpio = texto.replace(/\*/g, '');
    
    const utterThis = new SpeechSynthesisUtterance(textoLimpio);
    utterThis.lang = 'es-PE';
    utterThis.rate = 1.0;
    
    // Obtener voces disponibles e intentar usar una latina/Google
    const voces = window.speechSynthesis.getVoices();
    const vozPreferida = voces.find(v => 
        (v.name.includes('Google') || v.name.includes('Microsoft')) && v.lang.includes('es')
    );
    
    if (vozPreferida) {
        utterThis.voice = vozPreferida;
    }
    
    window.speechSynthesis.speak(utterThis);
}
