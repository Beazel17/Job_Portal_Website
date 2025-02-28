async function scanFile() {
    const fileInput = document.getElementById('fileInput');
    const resultDiv = document.getElementById('result');
    const messageDiv = document.getElementById('scanMessage');

    if (fileInput.files.length === 0) {
        resultDiv.innerHTML = "❌ Please select a file.";
        messageDiv.innerHTML = "⚠️ No file selected.";
        return;
    }

    const file = fileInput.files[0];
    const fileName = file.name.toLowerCase();
    const fileExt = fileName.split('.').pop();
    const dangerousExtensions = ['exe', 'bat', 'vbs', 'cmd', 'js', 'scr', 'ps1', 'jar'];

    resultDiv.innerHTML = "🔍 Scanning...";
    messageDiv.innerHTML = "Scanning in progress...";

    if (dangerousExtensions.includes(fileExt)) {
        resultDiv.innerHTML = `⚠️ This file (${fileExt}) could be harmful!`;
        resultDiv.className = "danger";
        messageDiv.innerHTML = "🚨 Potential malware detected!";
        return;
    }

    const reader = new FileReader();
    reader.onload = async function (e) {
        const fileContent = e.target.result;

        const suspiciousKeywords = ['eval', 'exec', 'shell', 'base64_decode', 'document.write', 'atob', 'obfuscate'];
        let foundSuspicious = suspiciousKeywords.some(keyword => fileContent.includes(keyword));

        if (foundSuspicious) {
            resultDiv.innerHTML = `⚠️ Suspicious script detected!`;
            resultDiv.className = "danger";
            messageDiv.innerHTML = "⚠️ This file might be dangerous!";
            return;
        }
        const hash = await computeSHA256(fileContent);
        console.log("File Hash:", hash);

        resultDiv.innerHTML = `✅ This file appears safe! (SHA-256: ${hash})`;
        resultDiv.className = "safe";
        messageDiv.innerHTML = "✔️ The file is safe to use!";
    };

    reader.readAsText(file);
}

async function computeSHA256(content) {
    const encoder = new TextEncoder();
    const data = encoder.encode(content);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}
