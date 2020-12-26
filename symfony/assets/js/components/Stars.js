import React from 'react';

import Rating from "react-rating";

import { faStar } from "@fortawesome/free-solid-svg-icons";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

class Stars extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            label: this.props.label || null,
            score: this.props.score || null
        };
    }

    render() {
        return (
            <div className="stars">
                {this.state.label}:&nbsp;
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