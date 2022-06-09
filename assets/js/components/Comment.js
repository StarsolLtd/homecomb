import React from 'react'
import Moment from 'react-moment'
import { faUser } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

import '../../styles/comment.scss'

const Comment = (props) => {
  return (
    <div className="comment">
      <hr/>
      <h6>
        <FontAwesomeIcon icon={faUser} className="text-primary"/>
        {' '}<span className="author">{props.author}</span>
        {' '}responded on <span className="date"><Moment format="Do MMM YYYY">{props.createdAt}</Moment></span>
      </h6>
      <p>
        {props.content}
      </p>
    </div>
  )
}

export default Comment
