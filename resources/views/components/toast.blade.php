{{-- Toast Notification Component --}}
<div class="toast-container" id="toastContainer">
    @if(session('success'))
    <div class="toast-notification toast-success" onclick="this.classList.add('toast-exit');setTimeout(()=>this.remove(),300)">
        <i class="fas fa-check-circle toast-icon"></i>
        <span class="toast-message">{{ session('success') }}</span>
        <button class="toast-close" onclick="event.stopPropagation();this.parentElement.classList.add('toast-exit');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        <div class="toast-progress"></div>
    </div>
    @endif
    @if(session('error'))
    <div class="toast-notification toast-error" onclick="this.classList.add('toast-exit');setTimeout(()=>this.remove(),300)">
        <i class="fas fa-exclamation-circle toast-icon"></i>
        <span class="toast-message">{{ session('error') }}</span>
        <button class="toast-close" onclick="event.stopPropagation();this.parentElement.classList.add('toast-exit');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        <div class="toast-progress"></div>
    </div>
    @endif
    @if(session('warning'))
    <div class="toast-notification toast-warning" onclick="this.classList.add('toast-exit');setTimeout(()=>this.remove(),300)">
        <i class="fas fa-exclamation-triangle toast-icon"></i>
        <span class="toast-message">{{ session('warning') }}</span>
        <button class="toast-close" onclick="event.stopPropagation();this.parentElement.classList.add('toast-exit');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        <div class="toast-progress"></div>
    </div>
    @endif
    @if(session('info'))
    <div class="toast-notification toast-info" onclick="this.classList.add('toast-exit');setTimeout(()=>this.remove(),300)">
        <i class="fas fa-info-circle toast-icon"></i>
        <span class="toast-message">{{ session('info') }}</span>
        <button class="toast-close" onclick="event.stopPropagation();this.parentElement.classList.add('toast-exit');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        <div class="toast-progress"></div>
    </div>
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.toast-notification').forEach(t => {
        setTimeout(() => { t.classList.add('toast-exit'); setTimeout(() => t.remove(), 300); }, 6000);
    });
});
</script>
