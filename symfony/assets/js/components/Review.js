import React, {Fragment} from 'react';
import ReviewStars from "./ReviewStars";
import Moment from 'react-moment';
import ReviewOptions from "./ReviewOptions";
import {Link} from "react-router-dom";

class Review extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            showProperty: this.props.hasOwnProperty('showProperty') ? this.props.showProperty : true,
            showBranch: this.props.hasOwnProperty('showBranch') ? this.props.showBranch : true,
            showAgency: this.props.hasOwnProperty('showAgency') ? this.props.showAgency : true,
        };
    }

    render() {
        return (
            <div className="reviews-members pt-4 pb-4">
                <div className="dropdown float-right review-options">
                    <ReviewOptions reviewId={this.props.id} {...this.props} />
                </div>

                <div className="reviews-members-header">
                    <p className="mb-1 font-weight-bold">
                        <span className="author">{this.props.author}</span>
                        {this.props.property && this.state.showProperty &&
                            <span>&nbsp;review of <Link to={'/property/' + this.props.property.slug}>{this.props.property.addressLine1}, {this.props.property.postcode}</Link></span>
                        }
                    </p>
                    <p className="text-gray">
                        <Moment format="Do MMMM YYYY">{this.props.createdAt}</Moment>
                    </p>
                </div>

                <div className="reviews-members-body">
                    <p className="font-weight-bold">{this.props.title}</p>

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
            </div>
        );
    }
}

export default Review;