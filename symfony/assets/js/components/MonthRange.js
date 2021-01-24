import React from 'react';
import Moment from 'react-moment';

const MonthRange = (props) => {
    return (
        <span className="month-range">
            {props.start !== null &&
                <span className="month-range-from">
                    from <Moment format="MMM YYYY" className="month-range-start">{props.start}</Moment>
                </span>
            }
            {' '}
            {props.end !== null &&
                <span className="month-range-to">
                    to <Moment format="MMM YYYY" className="month-range-end">{props.end}</Moment>
                </span>
            }
        </span>
    );
}

export default MonthRange;