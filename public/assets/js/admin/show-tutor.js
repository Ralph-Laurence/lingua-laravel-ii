window.addEventListener('DOMContentLoaded', function()
{
    bindEvents();
});

function bindEvents()
{
    let terminateBtn = document.querySelector('#btn-terminate');

    if (terminateBtn)
    {
        terminateBtn.addEventListener('click', function(e)
        {
            let name = e.currentTarget.dataset.tutorName;
            let prompt = `Would you like to terminate the user account for tutor "<b>${name}</b>"?`;
            prompt = DOMPurify.sanitize(prompt);

            ConfirmBox.show(prompt, 'Terminate Tutor',
            {
                onOK: () => {
                    showWaiting();
                    let form = document.querySelector('#terminateTutorForm');
                    form.submit();
                }
            });
        });
    }
}
