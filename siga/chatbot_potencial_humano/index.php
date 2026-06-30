<?php
session_start();
// Fetch user's first name from session if available, otherwise default to a generic greeting
$nombreUsuario = isset($_SESSION['nomb_oper']) ? ucfirst(strtolower(explode(' ', trim($_SESSION['nomb_oper']))[0])) : 'Compañero(a)';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Potencial Humano - MPFN Arequipa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chat-container">
        <!-- Header del Chat (Moderno) -->
        <div class="chat-header modern-header">
            <div class="header-left">
                <button class="header-back-btn" id="headerBackBtn" style="display: none;">
                    <i class="fas fa-arrow-left"></i> <span id="headerBackText">Atrás</span>
                </button>
            </div>
            <div class="header-right">
                <div class="header-badge">Consultas Laborales</div>
                <div class="user-avatar-header" style="background: none;">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImJnIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjZmY5YTllIi8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjZmVjZmVmIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9InVybCgjYmcpIi8+PGc+PGFuaW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIiB0eXBlPSJ0cmFuc2xhdGUiIHZhbHVlcz0iMCwwOyAwLDE7IDAsMCIgZHVyPSIycyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48cGF0aCBkPSJNMjUgNjAgUTIwIDIwIDUwIDE1IFE4MCAyMCA3NSA2MCBMODAgODUgUTUwIDk1IDIwIDg1IFoiIGZpbGw9IiM0YTJlMWIiLz48cmVjdCB4PSI0MiIgeT0iNjUiIHdpZHRoPSIxNiIgaGVpZ2h0PSIyMCIgcng9IjUiIGZpbGw9IiNmZmNjYTciLz48cGF0aCBkPSJNMjAgMTAwIFE1MCA2NSA4MCAxMDAgWiIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik00MiA4MCBMNTAgODggTDU4IDgwIFoiIGZpbGw9IiNmMGYwZjAiLz48cmVjdCB4PSIzMCIgeT0iMzAiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MiIgcng9IjIwIiBmaWxsPSIjZmZlMGM4Ii8+PHBhdGggZD0iTTMwIDQ1IFE0MCAyNSA3MCAzNSBRNTAgMjAgMzAgNDUgWiIgZmlsbD0iIzVjM2EyMSIvPjxnPjxjaXJjbGUgY3g9IjQwIiBjeT0iNDgiIHI9IjMuNSIgZmlsbD0iIzMzMyI+PGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iciIgdmFsdWVzPSIzLjU7IDMuNTsgMDsgMy41OyAzLjUiIGtleVRpbWVzPSIwOyAwLjk7IDAuOTU7IDAuOTg7IDEiIGR1cj0iNHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+PC9jaXJjbGU+PGNpcmNsZSBjeD0iNjAiIGN5PSI0OCIgcj0iMy41IiBmaWxsPSIjMzMzIj48YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJyIiB2YWx1ZXM9IjMuNTsgMy41OyAwOyAzLjU7IDMuNSIga2V5VGltZXM9IjA7IDAuOTsgMC45NTsgMC45ODsgMSIgZHVyPSI0cyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48L2NpcmNsZT48L2c+PHBhdGggZD0iTTM2IDQ2IFE0MCA0MyA0NCA0NiIgc3Ryb2tlPSIjMzMzIiBzdHJva2Utd2lkdGg9IjEuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PHBhdGggZD0iTTU2IDQ2IFE2MCA0MyA2NCA0NiIgc3Ryb2tlPSIjMzMzIiBzdHJva2Utd2lkdGg9IjEuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PGNpcmNsZSBjeD0iMzUiIGN5PSI1NCIgcj0iNCIgZmlsbD0iI2ZmOTk5OSIgb3BhY2l0eT0iMC41Ii8+PGNpcmNsZSBjeD0iNjUiIGN5PSI1NCIgcj0iNCIgZmlsbD0iI2ZmOTk5OSIgb3BhY2l0eT0iMC41Ii8+PHBhdGggZD0iTTQ1IDU4IFE1MCA2NCA1NSA1OCIgc3Ryb2tlPSIjZDY1YTVhIiBzdHJva2Utd2lkdGg9IjIuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9nPjwvc3ZnPg==" alt="Header Avatar" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 8px rgba(0,0,0,0.2);">
                </div>
            </div>
        </div>

        <!-- Área de mensajes -->
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImJnIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjZmY5YTllIi8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjZmVjZmVmIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9InVybCgjYmcpIi8+PGc+PGFuaW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIiB0eXBlPSJ0cmFuc2xhdGUiIHZhbHVlcz0iMCwwOyAwLDE7IDAsMCIgZHVyPSIycyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48cGF0aCBkPSJNMjUgNjAgUTIwIDIwIDUwIDE1IFE4MCAyMCA3NSA2MCBMODAgODUgUTUwIDk1IDIwIDg1IFoiIGZpbGw9IiM0YTJlMWIiLz48cmVjdCB4PSI0MiIgeT0iNjUiIHdpZHRoPSIxNiIgaGVpZ2h0PSIyMCIgcng9IjUiIGZpbGw9IiNmZmNjYTciLz48cGF0aCBkPSJNMjAgMTAwIFE1MCA2NSA4MCAxMDAgWiIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik00MiA4MCBMNTAgODggTDU4IDgwIFoiIGZpbGw9IiNmMGYwZjAiLz48cmVjdCB4PSIzMCIgeT0iMzAiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MiIgcng9IjIwIiBmaWxsPSIjZmZlMGM4Ii8+PHBhdGggZD0iTTMwIDQ1IFE0MCAyNSA3MCAzNSBRNTAgMjAgMzAgNDUgWiIgZmlsbD0iIzVjM2EyMSIvPjxnPjxjaXJjbGUgY3g9IjQwIiBjeT0iNDgiIHI9IjMuNSIgZmlsbD0iIzMzMyI+PGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iciIgdmFsdWVzPSIzLjU7IDMuNTsgMDsgMy41OyAzLjUiIGtleVRpbWVzPSIwOyAwLjk7IDAuOTU7IDAuOTg7IDEiIGR1cj0iNHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+PC9jaXJjbGU+PGNpcmNsZSBjeD0iNjAiIGN5PSI0OCIgcj0iMy41IiBmaWxsPSIjMzMzIj48YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJyIiB2YWx1ZXM9IjMuNTsgMy41OyAwOyAzLjU7IDMuNSIga2V5VGltZXM9IjA7IDAuOTsgMC45NTsgMC45ODsgMSIgZHVyPSI0cyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48L2NpcmNsZT48L2c+PHBhdGggZD0iTTM2IDQ2IFE0MCA0MyA0NCA0NiIgc3Ryb2tlPSIjMzMzIiBzdHJva2Utd2lkdGg9IjEuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PHBhdGggZD0iTTU2IDQ2IFE2MCA0MyA2NCA0NiIgc3Ryb2tlPSIjMzMzIiBzdHJva2Utd2lkdGg9IjEuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PGNpcmNsZSBjeD0iMzUiIGN5PSI1NCIgcj0iNCIgZmlsbD0iI2ZmOTk5OSIgb3BhY2l0eT0iMC41Ii8+PGNpcmNsZSBjeD0iNjUiIGN5PSI1NCIgcj0iNCIgZmlsbD0iI2ZmOTk5OSIgb3BhY2l0eT0iMC41Ii8+PHBhdGggZD0iTTQ1IDU4IFE1MCA2NCA1NSA1OCIgc3Ryb2tlPSIjZDY1YTVhIiBzdHJva2Utd2lkdGg9IjIuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9nPjwvc3ZnPg==" alt="Bot Avatar" class="message-avatar bot-avatar-img animated-avatar">
                <div class="message-wrapper">
                    <div class="message-content">
                        <p>¡Hola <?php echo htmlspecialchars($nombreUsuario); ?>! Soy tu asistente de Potencial Humano. Elige un tema o escribe tu consulta:</p>
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
                        </div>
                    </div>
                    <div class="message-time">Ahora</div>
                </div>
            </div>
        </div>

        <!-- Indicador de escritura -->
        <div class="typing-indicator" id="typingIndicator" style="display: none;">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>

        <!-- Área de entrada -->
        <div class="chat-input-container">
            <form id="chatForm" class="chat-input-form">
                <textarea 
                    id="userInput" 
                    class="chat-input" 
                    placeholder="Escribe tu mensaje aquí..."
                    rows="1"
                    maxlength="500"
                ></textarea>
                <button type="submit" class="send-button" id="sendButton">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </form>
            <div class="input-footer">
                <small>Presiona Enter para enviar, Shift+Enter para nueva línea</small>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
