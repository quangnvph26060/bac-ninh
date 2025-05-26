<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Import đơn hàng</title>
    <style>
        .error-list {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ffcdd2;
            background-color: #ffebee;
            border-radius: 4px;
            color: #c62828;
        }

        .error-item {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .error-item:before {
            content: "•";
            position: absolute;
            left: 0;
        }

        .success-message {
            color: #2e7d32;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div id="progress"></div>
    <div id="result"></div>

    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required />
        <button type="submit">Submit</button>
    </form>

    <script>
        const progressDiv = document.getElementById('progress');
        const resultDiv = document.getElementById('result');
        const form = document.getElementById('uploadForm');
        let interval;
        let jobId;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            resultDiv.innerHTML = ''; // Clear previous results

            const formData = new FormData(form);

            // Gửi file lên server qua fetch POST (AJAX)
            const res = await fetch('/import-orders', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            const data = await res.json();

            if (data.job_id) {
                jobId = data.job_id;
                progressDiv.innerText = 'Bắt đầu import...';

                // Bắt đầu poll mỗi 2s để lấy tiến trình
                interval = setInterval(async () => {
                    try {
                        const progressRes = await fetch(`/import-progress/${jobId}`);
                        const progressData = await progressRes.json();

                        if (!progressData) {
                            progressDiv.innerText = `Lỗi: Không nhận được phản hồi từ server`;
                            clearInterval(interval);
                            return;
                        }

                        if (progressData.status === 'success') {
                            progressDiv.innerText = `Hoàn tất! ✅`;
                            resultDiv.innerHTML =
                                '<div class="success-message">Import thành công!</div>';
                            clearInterval(interval);
                        } else if (progressData.status === 'completed_with_errors' || progressData
                            .status === 'completed') {
                            progressDiv.innerText = `Hoàn tất với lỗi! ⚠️`;
                            displayFailures(progressData.failures);
                            clearInterval(interval);
                        } else if (progressData.status === 'failed') {
                            progressDiv.innerText = `Lỗi! ❌`;
                            resultDiv.innerHTML =
                                `<div class="error-list">Lỗi: ${progressData.error}</div>`;
                            clearInterval(interval);
                        } else {
                            progressDiv.innerText =
                                `${progressData.current}/${progressData.total} (${progressData.percent}%)`;
                        }
                    } catch (error) {
                        progressDiv.innerText = `Lỗi: ${error.message}`;
                        clearInterval(interval);
                    }
                }, 2000);

                // Safety timeout - stop polling after 5 minutes
                setTimeout(() => {
                    if (interval) {
                        clearInterval(interval);
                        progressDiv.innerText = `Đã hết thời gian chờ`;
                    }
                }, 5 * 60 * 1000);
            } else {
                progressDiv.innerText = 'Lỗi khi bắt đầu import';
            }
        });

        function displayFailures(failures) {
            if (!failures || failures.length === 0) return;

            const errorList = document.createElement('div');
            errorList.className = 'error-list';
            errorList.innerHTML = '<strong>Danh sách lỗi:</strong>';

            failures.forEach(error => {
                const errorItem = document.createElement('div');
                errorItem.className = 'error-item';
                errorItem.textContent = error;
                errorList.appendChild(errorItem);
            });

            resultDiv.innerHTML = '';
            resultDiv.appendChild(errorList);
        }
    </script>

</body>

</html>
