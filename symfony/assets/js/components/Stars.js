import React from 'react';

import Rating from "react-rating";

import { faStar } from "@fortawesome/free-solid-svg-icons";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

class Stars extends React.Component {
    render() {
        return (
            <div className="stars">
                {this.props.label}:&nbsp;
                <Rating
                    readonly
                    initialRating={this.props.score}
                    emptySymbol={
                        <span className="text-rating-unchecked">
                            <FontAwesomeIcon icon={faStar} />
                        </span>
                    }
                    fullSymbol={
                        <span className="text-rating">
                            <FontAwesomeIcon icon={faStar} />
                        </span>
                    }
                />
            </div>
        );
    }
}

export default Stars;