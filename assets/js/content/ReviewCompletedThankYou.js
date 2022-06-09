import React from 'react'
import { Alert } from 'reactstrap'

const ReviewCompletedThankYou = () => {
  return (
    <Alert color="success" className="p-4 text-center alert-success alert-dismissible fade show review-completed-thank-you">
      <h2>Thank you!</h2>

      <p>
        Your review was received successfully and will be checked by our moderation team shortly.
      </p>

      <button type="button" className="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </Alert>
  )
}

export default ReviewCompletedThankYou
