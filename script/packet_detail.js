// packet_detail.js

function confirmAction(action) {
    return confirm('คุณต้องการทำการ ' + action + ' ข้อมูลนี้หรือไม่?');
}

document.querySelector('button[name="update"]').addEventListener('click', function(event) {
    if (!confirmAction('อัพเดต')) {
        event.preventDefault();
    }
});

document.querySelector('button[name="add_tourist"]').addEventListener('click', function(event) {
    if (!confirmAction('เพิ่ม')) {
        event.preventDefault();
    }
});
