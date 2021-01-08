import React from 'react';
import Moment from 'react-moment';

import '../../styles/comment.scss';

const Comment = (props) => {
    return (
        <div className="comment">
            <h6>
                Response from <span className="author">{ props.author }</span>{' '}
                on <span className="date"><Moment format="Do MMMM YYYY">{props.createdAt}</Moment></span>
            </h6>
            <p>
                { props.content }
            </p>
        </div>
    );
}

export default Comment;