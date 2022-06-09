import React, { Fragment } from 'react'
import { Alert } from 'reactstrap'

const FlashMessages = (props) => {
  return (
    <Fragment>
      {props.messages.map(
        ({key, context, content}) => (
          <Alert key={key} color={context} className="alert-dismissible fade show">
            {content}
            <button type="button" className="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </Alert>
        )
      )}
    </Fragment>
  )
}

export default FlashMessages
