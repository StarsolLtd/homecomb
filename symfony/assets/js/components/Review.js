import React, {Fragment} from 'react';
import ReviewStars from "./ReviewStars";
import Moment from 'react-moment';
import ReviewOptions from "./ReviewOptions";

class Review extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            id: this.props.id,
            author: this.props.author,
            title: this.props.title,
            content: this.props.content,
            property: this.props.property,
            branch: this.props.branch,
            agency: this.props.agency,
            stars: this.props.stars,
            createdAt: this.props.createdAt,
            showProperty: this.props.hasOwnProperty('showProperty') ? this.props.showProperty : true,
            showBranch: this.props.hasOwnProperty('showBranch') ? this.props.showBranch : true,
            showAgency: this.props.hasOwnProperty('showAgency') ? this.props.showAgency : true,
        };
    }

    render() {
        return (
            <div className="reviews-members pt-4 pb-4">
                <div className="dropdown float-right">
                    <ReviewOptions reviewId={this.state.id} />
                </div>

                <div className="reviews-members-header">
                    <p className="mb-1 font-weight-bold">
                        <span className="author">{this.state.author}</span>
                        {this.state.property && this.state.showProperty &&
                            <span>&nbsp;review of <a href={'/property/' + this.state.property.slug}>{this.state.property.addressLine1}, {this.state.property.postcode}</a></span>
                        }
                    </p>
                    <p className="text-gray">
                        <Moment format="Do MMMM YYYY">{this.state.createdAt}</Moment>
                    </p>
                </div>

                <div className="reviews-members-body">
                    <p className="font-weight-bold">{this.state.title}</p>

                    <p>{this.state.content}</p>

                    {this.state.branch && this.state.showBranch &&
                    <p>
                        {this.state.agency && this.state.showAgency &&
                        <Fragment>
                            Agency:&nbsp;
                            {this.state.agency.published &&
                            <a href={'/agency/' + this.state.agency.slug} className="agency-name">{this.state.agency.name}</a>
                            }
                            {!this.state.agency.published &&
                            <span className="agency-name">{this.state.agency.name}</span>
                            }
                            <br/>
                        </Fragment>
                        }
                        Branch:&nbsp;
                        {this.state.branch.published &&
                            <a href={'/branch/' + this.state.branch.slug} className="branch-name">{this.state.branch.name}</a>
                        }
                        {!this.state.branch.published &&
                            <span className="branch-name">{this.state.branch.name}</span>
                        }
                        <br />
                    </p>
                    }
                </div>

                <ReviewStars
                    overall={this.state.stars.overall}
                    agency={this.state.stars.agency}
                    landlord={this.state.stars.landlord}
                    property={this.state.stars.property}
                />
            </div>
        );
    }
}

export default Review;