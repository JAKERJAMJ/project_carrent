// Preview Img

const fileInput = document.getElementById('packet_main_picture');
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

// delete packet 
function deletePacket(packet_id) {
    if(confirm('ต้องการยกเลิกการใช้งานรถ ID ' + packet_id + '?')) {
    // ส่งคำขอไปยังไฟล์ PHP พร้อมกับ ID ของรถยนต์
        fetch('delete_packet.php?id=' + packet_id, {
            method: 'GET'
        })
    .then(response => response.text())
    .then(data => {
        alert(data); // แสดงข้อความจากการตอบกลับของ PHP
        window.location.reload(); // โหลดหน้าใหม่เพื่ออัพเดทข้อมูล
    })
    .catch(error => console.error('Error:', error));
    }
}