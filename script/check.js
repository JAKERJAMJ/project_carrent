function showCheckMember(car_id, carrent_date, carrent_return) {
    document.getElementById('car_id').value = car_id;
    document.getElementById('carrent_date_hidden').value = carrent_date;
    document.getElementById('carrent_return_hidden').value = carrent_return;
    var checkMemberModal = new bootstrap.Modal(document.getElementById('CheckMemberModal'));
    checkMemberModal.show();
}

function showConfirmMemberModal(message, confirmCallback) {
    document.getElementById('confirmModalBody').innerText = message;
    var confirmButton = document.getElementById('confirmButton');
    var confirmModal = new bootstrap.Modal(document.getElementById('ConfirmMemberModal'));
    confirmButton.onclick = function() {
        confirmCallback();
        confirmModal.hide();
    };
    confirmModal.show();
}