import React from 'react'
import { Alert } from 'reactstrap'

const InternalServerError = () => {
  return (
    <Alert color="error">
      500 Internal Server Error. It&apos;s not you, it&apos;s us.
    </Alert>
  )
}

export default InternalServerError
