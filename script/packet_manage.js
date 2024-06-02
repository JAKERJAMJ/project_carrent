// Preview Img

const fileInput = document.getElementById('package_picture');
const imagePreview = document.getElementById('image-preview');
const fileInputLabel = document.getElementById('file-input-label');
    
fileInput.addEventListener('change', (event) => {
    const selectedFile = event.target.files[0];
            
    if (selectedFile) {
        const reader = new FileReader();
    
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            fileInputLabel.innerText = 'เลือกไฟล์อื่น';
        };
    
        reader.readAsDataURL(selectedFile);
    } else {
        imagePreview.src = '';
        imagePreview.style.display = 'none';
        fileInputLabel.innerText = 'เลือกไฟล์รูปภาพ';
    }
});


// js script popup
function showPopup() {
    // ดึงองค์ประกอบของป็อปอัพมา
    var popup = document.getElementById("add_packet");
    // แสดงป็อปอัพ
    popup.style.display = "block";
}

function hidePopup() {
    // ดึงองค์ประกอบของป็อปอัพมา
    var popup = document.getElementById("add_packet");
    // ซ่อนป็อปอัพ
    popup.style.display = "none";
}

// update packet 
function updatePackageStatus(packageId) {
    if (confirm('คุณแน่ใจหรือว่าต้องการยกเลิกการใช้งานแพ็คเกจนี้?')) {
        fetch('update_package_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ package_id: packageId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('สถานะแพ็คเกจถูกยกเลิกเรียบร้อยแล้ว');
                window.location.reload();
            } else {
                alert('เกิดข้อผิดพลาดในการยกเลิกการใช้งานแพ็คเกจ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
