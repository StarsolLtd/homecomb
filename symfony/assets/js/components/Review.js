import React, {Fragment} from 'react';
import ReviewStars from "./ReviewStars";
import Moment from 'react-moment';
import ReviewOptions from "./ReviewOptions";
import {Link} from "react-router-dom";
import Comment from "./Comment";
import { faUser } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

import '../../styles/review.scss';
import MonthRange from "./MonthRange";

class Review extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            showProperty: this.props.hasOwnProperty('showProperty') ? this.props.showProperty : true,
            showBranch: this.props.hasOwnProperty('showBranch') ? this.props.showBranch : true,
            showAgency: this.props.hasOwnProperty('showAgency') ? this.props.showAgency : true,
            showOptions: this.props.hasOwnProperty('showOptions') ? this.props.showOptions : true,
        };
    }

    render() {
        return (
            <div className={'review pt-4 pb-4 ' + this.props.className}>
                {this.state.showOptions &&
                    <div className="dropdown float-right review-options">
                        <ReviewOptions reviewId={this.props.id} {...this.props} />
                    </div>
                }

                <div>
                    <p className="mb-3">
                        <FontAwesomeIcon icon={faUser} className="text-primary" />
                        {' '}<span className="author">{this.props.author}</span>
                        {' '}reviewed their tenancy <MonthRange start={this.props.start} end={this.props.end} />
                        {this.props.property && this.state.showProperty &&
                            <span>&nbsp;at <Link to={'/property/' + this.props.property.slug}>{this.props.property.addressLine1}, {this.props.property.postcode}</Link></span>
                        }
                        <span className="review-date"><br />Date of review: <Moment format="Do MMM YYYY" className="date">{this.props.createdAt}</Moment></span>
                    </p>
                </div>

                <div>
                    <h3>{this.props.title}</h3>

                    <p>{this.props.content}</p>

                    {this.props.branch && this.state.showBranch &&
                    <p>
                        {this.props.agency && this.state.showAgency &&
                        <Fragment>
                            Agency:&nbsp;
                            {this.props.agency.published &&
                            <Link to={'/agency/' + this.props.agency.slug} className="agency-name">{this.props.agency.name}</Link>
                            }
                            {!this.props.agency.published &&
                            <span className="agency-name">{this.props.agency.name}</span>
                            }
                            <br/>
                        </Fragment>
                        }
                        Branch:&nbsp;
                        {this.props.branch.published &&
                            <Link to={'/branch/' + this.props.branch.slug} className="branch-name">{this.props.branch.name}</Link>
                        }
                        {!this.props.branch.published &&
                            <span className="branch-name">{this.props.branch.name}</span>
                        }
                        <br />
                    </p>
                    }
                </div>

                <ReviewStars
                    overall={this.props.stars.overall}
                    agency={this.props.stars.agency}
                    landlord={this.props.stars.landlord}
                    property={this.props.stars.property}
                />

                {this.props.comments && this.props.comments.map(
                    ({ id, author, content, createdAt }) => (
                        <Comment key={id} author={author} createdAt={createdAt} content={content} />
                    )
                )}
            </div>
        );
    }
}

export default Review;