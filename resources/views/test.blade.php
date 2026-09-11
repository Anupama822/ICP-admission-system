@extends('layouts.app')
@section('content')
<div class="icp-card mb-4 form-step" data-step="8">
    <div class="icp-card-header">
        <p class="icp-card-title">Digital Signature</p>
        <p class="icp-card-subtitle">Captured from the signotec signature pad connected to this computer.</p>
    </div>
    <div class="p-3">


        <div id="signotec-status" class="icp-note d-flex gap-3 p-3 rounded-3 mb-3">
            @svg('heroicon-m-information-circle', 'icp-icon-sm flex-shrink-0 mt-1')
            <p class="mb-0 small" id="signotec-status-text">Press "Capture Signature" to connect to the signotec pad.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <button type="button" id="js-signotec-start" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                @svg('heroicon-m-pencil', 'icp-icon-sm')
                <span>Capture Signature</span>
            </button>
            <button type="button" id="js-signotec-done" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2" style="display:none">
                @svg('heroicon-m-check', 'icp-icon-sm')
                <span>Done</span>
            </button>
            <button type="button" id="js-signotec-retry" class="btn icp-btn-muted px-4 py-2" style="display:none">Retry</button>
        </div>

        <p class="small mb-2" id="signotec-preview-label" style="display:none">Captured signature:</p>
        <img id="signotec-preview" alt="Captured signature" style="display:none;height:70px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:4px" class="mb-2 d-block">

        <input type="hidden" name="signature" id="signature-input">
        @error('signature') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/signotec/STPadServerLib.js') }}"></script>
<script>
(function () {
    const { STPadServerLibCommons, STPadServerLibDefault, STPadServerLibApi } = window.STPadServerLib;

    const wsUrl = "wss://127.0.0.1:49494"; // match your install option
    const statusText   = document.getElementById('signotec-status-text');
    const startBtn      = document.getElementById('js-signotec-start');
    const doneBtn        = document.getElementById('js-signotec-done');
    const retryBtn      = document.getElementById('js-signotec-retry');
    const preview       = document.getElementById('signotec-preview');
    const previewLabel  = document.getElementById('signotec-preview-label');
    const input          = document.getElementById('signature-input');

    let padIndex = null;

    function setStatus(msg) { statusText.textContent = msg; }

    function connect() {
        STPadServerLibCommons.createConnection(wsUrl, onOpen, onClose, onError);
    }

    async function onOpen() {
        try {
            setStatus('Looking for the signature pad...');
            const searchParams = new STPadServerLibDefault.Params.searchForPads();
            const found = await STPadServerLibDefault.searchForPads(searchParams);

            if (!found.foundPads.length) {
                setStatus('No signotec pad found. Check the USB connection and try again.');
                return;
            }

            padIndex = found.foundPads[0].index;
            await STPadServerLibDefault.openPad(new STPadServerLibDefault.Params.openPad(padIndex));

            const sigParams = new STPadServerLibDefault.Params.startSignature();
            sigParams.setFieldName('Signature');
            await STPadServerLibDefault.startSignature(sigParams);

            setStatus('Please sign on the pad, then press "Done".');
            startBtn.style.display  = 'none';
            doneBtn.style.display    = 'inline-flex';
            retryBtn.style.display  = 'inline-block';
        } catch (e) {
            setStatus('Error: ' + e.errorMessage);
        }
    }

    function onClose() { setStatus('Connection to the pad was closed.'); }
    function onError()  { setStatus('Could not reach the signotec service. Is it running on this PC?'); }

    startBtn.addEventListener('click', connect);

    doneBtn.addEventListener('click', async () => {
        try {
            const stopped = await STPadServerLibDefault.confirmSignature();
            if (stopped.countedPoints < 5) {
                setStatus('That looks empty — please sign again.');
                await STPadServerLibDefault.retrySignature();
                return;
            }

            const imgParams = new STPadServerLibDefault.Params.getSignatureImage();
            imgParams.setFileType(STPadServerLibDefault.FileType.PNG);
            const img = await STPadServerLibDefault.getSignatureImage(imgParams);

            input.value = img.file;
            preview.src = 'data:image/png;base64,' + img.file;
            preview.style.display = 'block';
            previewLabel.style.display = 'block';

            await STPadServerLibDefault.closePad(new STPadServerLibDefault.Params.closePad(padIndex));
            STPadServerLibCommons.destroyConnection();

            setStatus('Signature captured.');
            doneBtn.style.display   = 'none';
            retryBtn.style.display = 'none';
        } catch (e) {
            setStatus('Error: ' + e.errorMessage);
        }
    });

    retryBtn.addEventListener('click', async () => {
        try {
            await STPadServerLibDefault.retrySignature();
            setStatus('Cleared — please sign again.');
        } catch (e) {
            setStatus('Error: ' + e.errorMessage);
        }
    });
})();
</script>
@endpush