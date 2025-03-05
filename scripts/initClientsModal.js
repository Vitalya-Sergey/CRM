MicroModal.init();
document.querySelectorAll('.open').forEach((modal)=>{
    MicroModal.show(
    modal.getAttribute('id'),{
        onClose:(modal)=>{       
    const url = new URL(document.location);
    const searchParams = url.searchParams;
    searchParams.delete('send-email');
    searchParams.delete('edit-user');
    window.history.pushState({}, '', url.toString());
        }
    });
})