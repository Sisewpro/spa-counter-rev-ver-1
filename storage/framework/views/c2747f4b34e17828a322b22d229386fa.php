<form action="<?php echo e(route('timer-cards.start', $id)); ?>" method="POST" class="bg-base-100 shadow-md rounded-lg p-4 max-w-sm mx-auto">
    <?php echo csrf_field(); ?>
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-base-500"><?php echo e($cardName); ?></h2>
        <div class="flex space-x-2">
            <span class="cursor-pointer text-base hover:text-warning" onclick="openEditModal('<?php echo e($id); ?>', '<?php echo e($cardName); ?>', '<?php echo e($therapistName); ?>', '<?php echo e($time); ?>')">
                <!-- Ikon Edit -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path d="M5.433 13.917l1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                </svg>
            </span>
        </div>
    </div>

    <div class="mt-2 text-sm text-base-400">
        <p><?php echo e($therapistName); ?></p>
    </div>

    <div class="text-center mt-2">
        <label id="statusDisplay_<?php echo e($id); ?>" class="block text-xl font-bold text-accent uppercase"><?php echo e($status); ?></label>
        <div class="text-2xl font-mono countdown mt-3">
            <span id="hours_<?php echo e($id); ?>" style="--value:1;"></span> :
            <span id="minutes_<?php echo e($id); ?>" style="--value:30;"></span> :
            <span id="seconds_<?php echo e($id); ?>" style="--value:0;"></span>
        </div>
    </div>

    <div>
        <input type="text" id="customer_<?php echo e($id); ?>" name="customer" class="w-full text-center p-2 input rounded" placeholder="Customer" required>
    </div>

    <div class="flex justify-center space-x-2 items-center">
        <button id="startStopButton_<?php echo e($id); ?>" type="button" class="btn btn-primary btn-sm px-4 py-2 rounded">Mulai</button>

        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-sm btn-ghost m-1">Option</div>
            <ul tabindex="0" class="menu dropdown-content bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                <li><a href="#" data-session="45">+1 Session</a></li>
                <li><a href="#" data-session="90">+2 Sessions</a></li>
            </ul>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeTimerCard("<?php echo e($id); ?>", "01:30:00");
    });

    function initializeTimerCard(cardId, initialTime) {
        let timerInterval;
        let totalSeconds = parseTimeInput(initialTime);
        let isRunning = false;

        const hoursElement = document.getElementById('hours_' + cardId);
        const minutesElement = document.getElementById('minutes_' + cardId);
        const secondsElement = document.getElementById('seconds_' + cardId);
        const statusDisplay = document.getElementById('statusDisplay_' + cardId);
        const startStopButton = document.getElementById('startStopButton_' + cardId);
        const customerInput = document.getElementById('customer_' + cardId);

        // Menangkap event ketika klik pada dropdown session
        const dropdownLinks = document.querySelectorAll('.dropdown-content li a');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const additionalMinutes = parseInt(this.dataset.session, 10);
                addSession(additionalMinutes);
            });
        });

        function formatTimeFromSeconds(totalSeconds) {
            const hrs = Math.floor(totalSeconds / 3600);
            const mins = Math.floor((totalSeconds % 3600) / 60);
            const secs = totalSeconds % 60;
            return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function parseTimeInput(time) {
            const [hrs, mins, secs] = time.split(':').map(Number);
            return (hrs * 3600) + (mins * 60) + secs;
        }

        function updateTimerDisplay() {
            const hrs = Math.floor(totalSeconds / 3600);
            const mins = Math.floor((totalSeconds % 3600) / 60);
            const secs = totalSeconds % 60;

            hoursElement.style.setProperty('--value', hrs);
            minutesElement.style.setProperty('--value', mins);
            secondsElement.style.setProperty('--value', secs);

            // Ubah warna timer menjadi oranye jika waktu kurang dari atau sama dengan 15 menit (900 detik)
            if (totalSeconds <= 900 && totalSeconds > 300) {
                hoursElement.classList.add('text-warning');
                minutesElement.classList.add('text-warning');
                secondsElement.classList.add('text-warning');
            } else {
                hoursElement.classList.remove('text-warning');
                minutesElement.classList.remove('text-warning');
                secondsElement.classList.remove('text-warning');
            }

            // Ubah warna timer menjadi merah jika waktu habis (0 detik)
            if (totalSeconds <= 300 && totalSeconds > 0) {
                hoursElement.classList.add('text-error');
                minutesElement.classList.add('text-error');
                secondsElement.classList.add('text-error');
            } else {
                hoursElement.classList.remove('text-error');
                minutesElement.classList.remove('text-error');
                secondsElement.classList.remove('text-error');
            }
        }


        startStopButton.addEventListener('click', function () {
            if (isRunning) {
                stopTimer();
            } else {
                startTimer();
            }
        });

        function startTimer() {
            if (customerInput.value.trim() === "") {
                customerInput.classList.add('input-error');
                return;
            } else {
                customerInput.classList.remove('input-error');
            }

            isRunning = true;
            updateStatus('RUNNING');
            startStopButton.textContent = 'Stop';

            timerInterval = setInterval(() => {
                if (totalSeconds > 0) {
                    totalSeconds--;
                    updateTimerDisplay();
                } else {
                    stopTimer();
                }
            }, 1000);

            fetch(`/timer-cards/${cardId}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    customer: customerInput.value,
                    time: totalSeconds,
                    status: 'RUNNING'
                })
            });
        }

        function stopTimer() {
            clearInterval(timerInterval);
            isRunning = false;
            totalSeconds = parseTimeInput("01:30:00");
            updateTimerDisplay();
            updateStatus('READY');
            startStopButton.textContent = 'Mulai';

            fetch(`/timer-cards/${cardId}/stop`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: 'READY'
                })
            });
        }

        // Fungsi untuk menambah sesi waktu
        function addSession(additionalMinutes) {
            totalSeconds += additionalMinutes * 60; // Konversi menit ke detik
            updateTimerDisplay();

            // Jika ingin mengirim perubahan ke server
            fetch(`/timer-cards/${cardId}/add-session`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    additionalMinutes: additionalMinutes,
                    time: totalSeconds
                })
            });
        }

        function updateStatus(status) {
            statusDisplay.textContent = status;

            if (status === 'RUNNING') {
                statusDisplay.classList.remove('text-warning');
                statusDisplay.classList.add('text-accent');
            } else if (status === 'READY') {
                statusDisplay.classList.remove('text-accent');
                statusDisplay.classList.add('text-warning');
            }
        }


        updateTimerDisplay();
    }
</script>
<?php /**PATH C:\laragon\www\spa-counter-rev-ver-1-development\resources\views/components/timer-card.blade.php ENDPATH**/ ?>