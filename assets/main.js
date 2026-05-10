document.addEventListener('DOMContentLoaded', function() {
    // OTP Timer
    const timerEl = document.getElementById('timer');
    if (timerEl) {
        let time = 60;
        const interval = setInterval(() => {
            time--;
            timerEl.textContent = `${time}s remaining`;
            if (time <= 0) {
                clearInterval(interval);
                alert("OTP timeout. Please try again.");
                window.location.href = 'transfer.php';
            }
        }, 1000);
    }
});