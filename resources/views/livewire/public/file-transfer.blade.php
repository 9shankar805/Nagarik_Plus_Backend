<div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-4">
                ⚡ Peer-to-Peer Transfer
            </span>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight sm:text-5xl">
                Quick <span class="text-emerald-600">Share</span>
            </h1>
            <p class="mt-3 text-lg text-slate-600 font-medium">
                Instantly transfer files directly from your PC browser to your Nagarik+ Mobile App using a 6-digit PIN.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden" x-data="fileTransfer()">
            <div class="p-8 sm:p-12 text-center">
                
                <!-- Step 1: Select File -->
                <div x-show="step === 1">
                    <h2 class="text-2xl font-black text-slate-900 mb-2">Select a File to Send</h2>
                    <p class="text-slate-600 text-sm mb-8">The file is transferred directly browser-to-phone with zero server storage.</p>

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-emerald-300 border-dashed rounded-3xl hover:border-emerald-500 transition-all bg-slate-50/50">
                        <div class="space-y-1 text-center py-10">
                            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div class="flex text-base text-slate-700 justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl shadow-md transition-all">
                                    <span>Browse File to Transfer</span>
                                    <input id="file-upload" type="file" class="sr-only" @change="handleFileSelect">
                                </label>
                            </div>
                            <p class="text-xs text-slate-400 font-medium mt-3">Any file format • Fast local Wi-Fi transfer</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Show PIN and Wait for Connection -->
                <div x-show="step === 2" style="display: none;">
                    <h2 class="text-2xl font-black text-slate-900 mb-2">Waiting for connection...</h2>
                    <p class="text-slate-600 text-sm mb-8">Enter this 6-digit PIN in your Nagarik+ Mobile App to start the transfer.</p>
                    
                    <div class="bg-emerald-50 rounded-2xl p-8 mb-8 border-2 border-dashed border-emerald-200">
                        <div class="text-6xl font-black text-emerald-600 tracking-widest">{{ $pin }}</div>
                    </div>

                    <p class="text-sm font-bold text-slate-800" x-text="'Selected File: ' + (file?.name || '')"></p>
                </div>

                <!-- Step 3: Transferring -->
                <div x-show="step === 3" style="display: none;">
                    <h2 class="text-2xl font-black text-slate-900 mb-2">Transferring File...</h2>
                    <p class="text-slate-600 text-sm mb-8">Sending file directly to your mobile device.</p>

                    <div class="w-full max-w-md mx-auto">
                        <div class="flex justify-between text-xs font-bold text-slate-800 mb-2">
                            <span>Progress</span>
                            <span x-text="progress + '%'" class="text-emerald-600"></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-4 mb-4 overflow-hidden border border-slate-200">
                            <div class="bg-emerald-600 h-4 transition-all duration-300 rounded-full" x-bind:style="'width: ' + progress + '%'"></div>
                        </div>
                        <p class="text-xs text-slate-500 font-mono" x-text="transferSpeed"></p>
                    </div>
                </div>

                <!-- Step 4: Success -->
                <div x-show="step === 4" style="display: none;">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 mb-6 border border-emerald-200">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 mb-2">Transfer Complete!</h2>
                    <p class="text-slate-600 text-sm mb-8">Your file was securely received on your mobile device.</p>
                    
                    <button @click="reset" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-700 transition">
                        Transfer Another File
                    </button>
                </div>
            </div>
            
            <div class="bg-slate-50 px-8 py-6 border-t border-slate-200 sm:px-12 flex flex-col sm:flex-row items-center justify-between">
                <div class="flex items-center gap-3 text-slate-600 text-xs font-semibold">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Direct local P2P encrypted transfer — fast and secure
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Load SimplePeer from CDN for WebRTC -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/simple-peer/9.11.1/simplepeer.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileTransfer', () => ({
            step: 1, // 1: select, 2: wait, 3: transfer, 4: done
            file: null,
            progress: 0,
            transferSpeed: '',
            pin: '{{ $pin }}',
            peer: null,
            channel: null,
            
            init() {
                // Ensure Echo is loaded
                if (window.Echo) {
                    this.setupEcho();
                } else {
                    document.addEventListener('EchoLoaded', () => this.setupEcho());
                }
            },

            handleFileSelect(event) {
                if (event.target.files.length > 0) {
                    this.file = event.target.files[0];
                    this.step = 2; // Move to waiting state
                }
            },

            setupEcho() {
                this.channel = window.Echo.channel('transfer.' + this.pin);
                this.channel.listen('.WebRTCSignal', (e) => {
                    // Ignore our own signals
                    if (e.sender === 'pc') return;

                    console.log('Received signal:', e.type);

                    if (e.type === 'join') {
                        // Mobile app joined, initialize our Peer as the initiator
                        this.initPeer();
                    } else if (e.type === 'answer' || e.type === 'candidate') {
                        if (this.peer && e.data) {
                            this.peer.signal(e.data);
                        }
                    }
                });
            },

            initPeer() {
                console.log('Initializing WebRTC Peer...');
                this.peer = new SimplePeer({
                    initiator: true,
                    trickle: true,
                    config: { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] }
                });

                this.peer.on('signal', (data) => {
                    // Send signal to mobile
                    fetch('/api/v1/transfer/signal', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            pin: this.pin,
                            type: data.type || 'candidate',
                            data: data,
                            sender: 'pc'
                        })
                    });
                });

                this.peer.on('connect', () => {
                    console.log('WebRTC Connected!');
                    this.step = 3;
                    this.sendFile();
                });

                this.peer.on('error', (err) => {
                    console.error('WebRTC Error:', err);
                    alert('Connection failed.');
                    this.reset();
                });
            },

            sendFile() {
                // Send metadata first
                this.peer.send(JSON.stringify({
                    type: 'metadata',
                    name: this.file.name,
                    size: this.file.size,
                    mime: this.file.type
                }));

                // Read file in chunks and send
                const CHUNK_SIZE = 64 * 1024; // 64 KB
                let offset = 0;
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.peer.send(e.target.result);
                    offset += e.target.result.byteLength;
                    this.progress = Math.min(100, Math.round((offset / this.file.size) * 100));

                    if (offset < this.file.size) {
                        readSlice(offset);
                    } else {
                        // Done sending
                        this.peer.send(JSON.stringify({ type: 'eof' }));
                        setTimeout(() => {
                            this.step = 4;
                        }, 500);
                    }
                };

                const readSlice = (o) => {
                    const slice = this.file.slice(o, o + CHUNK_SIZE);
                    reader.readAsArrayBuffer(slice);
                };

                readSlice(0);
            },

            reset() {
                this.step = 1;
                this.file = null;
                this.progress = 0;
                if (this.peer) {
                    this.peer.destroy();
                    this.peer = null;
                }
                // Reload page to generate a new PIN
                window.location.reload();
            }
        }));
    });
</script>
@endpush
