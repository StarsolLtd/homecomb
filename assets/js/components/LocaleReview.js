import React from 'react';
import ReviewStars from "./ReviewStars";
import Moment from 'react-moment';
import { faUser } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

import '../../styles/review.scss';
import Vote from "./Vote";

class LocaleReview extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            showVote: this.props.hasOwnProperty('showVote') ? this.props.showVote : true,
        };
    }

    render() {
        return (
            <div className={'review pt-4 pb-4 ' + this.props.className}>
                <div>
                    <p className="mb-3">
                        <FontAwesomeIcon icon={faUser} className="text-primary" />
                        {' '}<span className="author">{this.props.author}</span>
                        <span className="review-date"><br />Date of review: <Moment format="Do MMM YYYY" className="date">{this.props.createdAt}</Moment></span>
                    </p>
                </div>

                <h3>{this.props.title}</h3>

                <p>{this.props.content}</p>

                <ReviewStars overall={this.props.overallStars} />

                {this.state.showVote &&
                    <Vote
                        className="mt-3"
                        entityName="LocaleReview"
                        entityId={this.props.id}
                        positiveTerm="Helpful"
                        positiveVotes={this.props.positiveVotes}
                    />
                }
            </div>
        );
    }
}

export default LocaleReview;