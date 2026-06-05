class SpeechSynthesizer {
    constructor() {
        this.synth = window.speechSynthesis;
        this.voices = [];
        this.selectedVoice = null;
        this.isSpeaking = false;
        this.queue = [];

        // Muat daftar suara yang tersedia
        this.loadVoices();
        if (this.synth.onvoiceschanged !== undefined) {
            this.synth.onvoiceschanged = () => this.loadVoices();
        }
    }

    loadVoices() {
        this.voices = this.synth.getVoices();
        // Coba cari suara Bahasa Indonesia
        this.selectedVoice = this.voices.find(voice => voice.lang === 'id-ID');
        // Jika tidak ada, gunakan suara default pertama yang tersedia
        if (!this.selectedVoice && this.voices.length > 0) {
            this.selectedVoice = this.voices[0];
        }
    }

    speak(text) {
        if (!this.selectedVoice || !text) {
            console.warn("Speech synthesis tidak siap atau tidak ada teks untuk diucapkan.");
            return;
        }

        // Tambahkan ke antrian
        this.queue.push(text);
        // Jika tidak sedang berbicara, mulai proses antrian
        if (!this.isSpeaking) {
            this.processQueue();
        }
    }

    processQueue() {
        if (this.queue.length === 0) {
            this.isSpeaking = false;
            return;
        }

        this.isSpeaking = true;
        const textToSpeak = this.queue.shift(); // Ambil item pertama dari antrian

        const utterance = new SpeechSynthesisUtterance(textToSpeak);
        utterance.voice = this.selectedVoice;
        utterance.lang = this.selectedVoice.lang;
        utterance.pitch = 1; // 0-2
        utterance.rate = 0.9; // 0.1-10
        utterance.volume = 1; // 0-1

        utterance.onend = () => {
            // Setelah selesai, proses item berikutnya di antrian
            this.processQueue();
        };

        utterance.onerror = (event) => {
            console.error('SpeechSynthesisUtterance.onerror', event);
            // Tetap proses antrian meskipun ada error
            this.processQueue();
        };

        this.synth.speak(utterance);
    }

    /**
     * Mengubah objek panggilan menjadi kalimat yang bisa diucapkan.
     * @param {object} panggilan - Objek panggilan dari API.
     * @returns {string} - Kalimat yang siap diucapkan.
     */
    formatPanggilan(panggilan) {
        if (!panggilan || !panggilan.nomor_lengkap || !panggilan.nama_loket) {
            return null;
        }

        const [kode, nomor] = panggilan.nomor_lengkap.split('-');
        const nomorTerbaca = parseInt(nomor, 10).toString(); // "005" -> "5"

        // Memecah nomor menjadi digit terpisah untuk dibaca satu per satu
        const nomorSplit = nomorTerbaca.split('').join('... '); // "101" -> "1... 0... 1"

        const namaLoketTerbaca = panggilan.nama_loket.replace(/(\d+)/g, ' $1 ');

        return `Nomor antrian... ${kode}... ${nomorSplit}, ...silakan menuju ke ${namaLoketTerbaca}`;
    }
}

// Export instance agar bisa digunakan di file lain
window.speechSynthesizer = new SpeechSynthesizer();
