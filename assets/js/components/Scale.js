import React from 'react';
import Rating from "react-rating";
import { faStar } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

import '../../styles/question.scss';

export default class Scale extends React.Component {

    handleRatingChange = (value) => {
        this.setState({rating: value});
    }

    render() {
        return (
            <div className="scale-5">
                <span className="meaning low-meaning">{this.props.lowMeaning}</span>
                <Rating
                    onChange={this.handleRatingChange}
                    initialRating={this.props.rating}
                    emptySymbol={
                        <span className="text-rating-unchecked rating-icon"><FontAwesomeIcon icon={faStar} /></span>
                    }
                    fullSymbol={
                        <span className="text-rating rating-icon"><FontAwesomeIcon icon={faStar} /></span>
                    }
                />
                <span className="meaning high-meaning">{this.props.highMeaning}</span>
            </div>
        );
    }
}
