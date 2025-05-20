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
            let name = e.currentTarget.dataset.learnerName;
            let prompt = `Would you like to terminate the user account for learner "<b>${name}</b>"?`;
            prompt = DOMPurify.sanitize(prompt);

            ConfirmBox.show(prompt, 'Terminate Learner',
            {
                onOK: () => {
                    showWaiting();
                    let form = document.querySelector('#terminateLearnerForm');
                    form.submit();
                }
            });
        });
    }
}
