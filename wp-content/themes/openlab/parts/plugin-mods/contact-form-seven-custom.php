<?php

?>

<p>
    <label for="fullName">Your Name (required)</label>
    [text* name class:form-control id:fullName]
</p>

<p>
    <label for="email">Your Email (required)</label>
    [email* email class:form-control id:email]
</p>

<p>
    <label for="contact-us-topic">Topic (required)</label>
    [select* topic id:contact-us-topic class:form-control "Request Help" "Report a Bug" "Request a Feature" "Make a Suggestion" "Leave a Comment" "Request a Workshop / Meeting" "Other"]
</p>

<div id="workshop-meeting-items">
    <p>
        <label for="group-type">Group Type (required)</label>
        [select group-type id:group-type class:form-control "Class" "Department" "Office" "Club" "Individual (faculty only)"]
    </p>

    <p>
        <label for="reason-for-request">Reason for Request (required)</label>
        [select reason-for-request id:reason-for-request class:form-control "Getting started/sign up" "Teaching users how to use course or other site" "ePortfolios" "Consultation" "Other (please specify)"]
    </p>

    <div id="other-details">
        <p>
            <label class="sr-only" for="other-details">Other Details</label>
            [text other-details id:other-details class:form-control]
        </p>
    </div>

    <p>
        <label for="number-of-participants">Number of Participants (required)</label>
        [text number-of-participants id:number-of-participants class:form-control]
    </p>

    <p>
        <label for="estimated-time-needed">Estimated time needed (required)</label>
        [text estimated-time-needed id:estimated-time-needed class:form-control]
    </p>

    <p>
        <label for="openlab-site">OpenLab Site (if applicable)</label><br />
        [text openlab-site id:openlab-site class:openlab-site]
    </p>

    <p>
        <label for="date-time-1st-choice">Date / Time (1st Choice)</label>
        [text date-time-1st-choice id:date-time-1st-choice class:form-control]
    </p>

    <p>
        <label for="date-time-2nd-choice">Date / Time (2nd Choice)</label>
        [text date-time-2nd-choice id:date-time-2nd-choice class:form-control]
    </p>

    <p>
        <label for="date-time-3rd-choice">Date / Time (3rd Choice)</label>
        [text date-time-3rd-choice id:date-time-3rd-choice class:form-control]
    </p>

    <p>
        <label for="need-computer-lab">Do you need a computer lab? If yes, requester is responsible for booking. (required)</label><br />
        [radio_accessible need-computer-lab id:need-computer-lab "Yes" "No"]
    </p>
</div>


<p>
    <label for="question">Question</label>
    [textarea question class:form-control id:question]
</p>

[submit class:btn class:btn-primary "Submit"]
