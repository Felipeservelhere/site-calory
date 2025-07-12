require('dotenv').config();
const express = require('express');
const bodyParser = require('body-parser');
const twilio = require('twilio');
const axios = require('axios');

const app = express();
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

// Configuração do Twilio
const accountSid = process.env.TWILIO_ACCOUNT_SID || 'AC972e1964cacb845151ddb3169231cb87';
const authToken = process.env.TWILIO_AUTH_TOKEN || '1a524b14afcdc4ff9385d3de49a30896';
const client = twilio(accountSid, authToken);

// Banco de dados simples (em produção, use um banco real)
const database = {};

// Rota para receber mensagens do WhatsApp
app.post('/whatsapp-webhook', async (req, res) => {
    const userPhone = req.body.From;
    const userMessage = req.body.Body.toLowerCase();
    const hasMedia = req.body.NumMedia > 0;

    // Inicializa o registro do usuário se não existir
    if (!database[userPhone]) {
        database[userPhone] = {
            step: 'welcome',
            documents: []
        };
    }

    let response;

    // Fluxo de conversação
    switch (database[userPhone].step) {
        case 'welcome':
            response = `Olá! Para emitir seu certificado digital, precisamos dos seguintes documentos:\n\n` +
                       `1️⃣ *Foto do RG ou CNH* (frente e verso)\n` +
                       `2️⃣ *Selfie segurando o documento*\n` +
                       `3️⃣ *Comprovante de residência*\n\n` +
                       `Por favor, envie os arquivos quando estiver pronto.`;
            database[userPhone].step = 'awaiting_documents';
            break;

        case 'awaiting_documents':
            if (hasMedia) {
                // Salva os documentos (em produção, armazene em um serviço como AWS S3)
                const mediaUrls = [];
                for (let i = 0; i < req.body.NumMedia; i++) {
                    mediaUrls.push(req.body[`MediaUrl${i}`]);
                }
                
                database[userPhone].documents = [...database[userPhone].documents, ...mediaUrls];
                database[userPhone].step = 'documents_received';
                
                response = `✅ Documento(s) recebido(s)! Precisamos agora agendar uma videochamada rápida para validação.\n\n` +
                           `Por favor, escolha um horário:\n\n` +
                           `1 - 09:00 às 10:00\n` +
                           `2 - 11:00 às 12:00\n` +
                           `3 - 14:00 às 15:00\n` +
                           `4 - 16:00 às 17:00`;
                
                // Notifica o atendente humano
                await notifyHumanAgent(userPhone, database[userPhone]);
            } else {
                response = `Por favor, envie os documentos solicitados para continuarmos.`;
            }
            break;

        case 'documents_received':
            const scheduleOptions = {
                '1': '09:00 às 10:00',
                '2': '11:00 às 12:00',
                '3': '14:00 às 15:00',
                '4': '16:00 às 17:00'
            };

            if (scheduleOptions[userMessage]) {
                database[userPhone].scheduledTime = scheduleOptions[userMessage];
                database[userPhone].step = 'scheduled';
                
                // Gera link da videochamada (exemplo com Google Meet)
                const meetingLink = generateMeetingLink();
                database[userPhone].meetingLink = meetingLink;
                
                response = `📅 Videochamada agendada para ${scheduleOptions[userMessage]}!\n\n` +
                           `Clique no link abaixo no horário marcado:\n` +
                           `${meetingLink}\n\n` +
                           `Obrigado por escolher nossos serviços!`;
                
                // Atualiza o atendente humano
                await notifyHumanAgent(userPhone, database[userPhone]);
            } else {
                response = `Por favor, escolha uma opção válida (1, 2, 3 ou 4).`;
            }
            break;

        default:
            response = `Por favor, envie "iniciar" para começar novamente.`;
    }

    // Responde no WhatsApp
    res.type('text/xml');
    res.send(`
        <Response>
            <Message>${response}</Message>
        </Response>
    `);
});

// Rota para enviar mensagens via API (opcional)
app.post('/send-message', async (req, res) => {
    try {
        const { to, body } = req.body;
        
        const message = await client.messages.create({
            body: body,
            from: 'whatsapp:+14155238886',
            to: `whatsapp:${to}`
        });

        res.json({ success: true, messageSid: message.sid });
    } catch (error) {
        console.error('Error sending message:', error);
        res.status(500).json({ success: false, error: error.message });
    }
});

// Função para notificar o atendente humano
async function notifyHumanAgent(customerPhone, customerData) {
    try {
        const formattedPhone = customerPhone.replace('whatsapp:+', '');
        const message = `📢 *NOVO CLIENTE PARA ATENDIMENTO* 📢\n\n` +
                       `📞 Telefone: ${formattedPhone}\n` +
                       `📄 Documentos recebidos: ${customerData.documents.length}\n` +
                       `⏰ Horário agendado: ${customerData.scheduledTime || 'Não agendado'}\n` +
                       `🔗 Link da chamada: ${customerData.meetingLink || 'Não gerado'}\n\n` +
                       `Clique para conversar: https://wa.me/${formattedPhone}`;

        await client.messages.create({
            body: message,
            from: 'whatsapp:+14155238886',
            to: 'whatsapp:+5544999939313' // Número do atendente
        });
    } catch (error) {
        console.error('Error notifying agent:', error);
    }
}

// Função para gerar link de videochamada (simulado)
function generateMeetingLink() {
    const randomId = Math.random().toString(36).substring(2, 15);
    return `https://meet.google.com/new?hs=191&authuser=0&${randomId}`;
}

// Inicia o servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Servidor rodando na porta ${PORT}`);
});