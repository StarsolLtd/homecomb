import React, {Fragment} from 'react';

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
            showProperty: this.props.showProperty || true,
            showBranch: this.props.showBranch || true,
            showAgency: this.props.showAgency || true
        };
    }

    render() {
        return (
            <div className="reviews-members pt-4 pb-4">
                <div className="dropdown float-right">
                    <button className="btn btn-light btn-ellipsis" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&hellip;</button>
                    <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a className="dropdown-item flag-review-link" data-entity-id="{{ review.id }}" href="#" data-toggle="modal" data-target="#flag-review-modal"><i className="fa fa-flag" /> Report this</a>
                    </div>
                </div>

                <div className="reviews-members-header">
                    <p className="mb-1 font-weight-bold">
                        <span className="author">{this.state.author}</span>
                        {this.state.property && this.state.showProperty &&
                            <span>&nbsp;review of <a href={'/property/' + this.state.property.slug}>{this.state.property.addressLine1}, {this.state.property.postcode}</a></span>
                        }
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
            </div>
        );
    }
}

export default Review;