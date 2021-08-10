import React, {Fragment} from 'react';
import {Container, Col, Row, Breadcrumb, BreadcrumbItem, Button} from 'reactstrap';
import Review from "../components/Review";
import RatedAgencies from "../components/RatedAgencies";
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";
import {Link} from "react-router-dom";
import LocaleReview from "../components/LocaleReview";
import ReviewLocaleForm from "../components/ReviewLocaleForm";
import ReviewCompletedThankYou from "../content/ReviewCompletedThankYou";

class LocaleView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            content: '',
            localeReviews: [],
            tenancyReviews: [],
            agencyReviewsSummary: null,
            loaded: false,
            reviewLocaleFormOpen: false,
            localeReviewCompletedThankYou: false,
        };

        this.loadData = this.loadData.bind(this);
        this.openReviewLocaleForm = this.openReviewLocaleForm.bind(this);
        this.localeReviewCompletedThankYou = this.localeReviewCompletedThankYou.bind(this);
    }

    openReviewLocaleForm() {
        this.setState({reviewLocaleFormOpen: true});
    }

    localeReviewCompletedThankYou() {
        this.setState({
            localeReviewCompletedThankYou: true,
            reviewLocaleFormOpen: false
        })
    }

    render() {
        return (
            <Container>
                {this.state.localeReviewCompletedThankYou &&
                    <ReviewCompletedThankYou />
                }
                <DataLoader
                    url={'/api/l/' + this.props.match.params.slug}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                    <div>
                        <Row>
                            <Breadcrumb className="w-100">
                                <BreadcrumbItem><Link to="/">Home</Link></BreadcrumbItem>
                                <BreadcrumbItem className="active locale-name">{this.state.name}</BreadcrumbItem>
                            </Breadcrumb>
                        </Row>
                        <Row className="bg-white rounded shadow-sm mb-4">
                            <Col md="6" className="p-4" dangerouslySetInnerHTML={{ __html: this.state.content }} />
                            <Col md="6" className="p-4">
                                <RatedAgencies
                                    heading={'Top rated agencies for lettings in ' + this.state.name}
                                    agencyReviewsSummary={this.state.agencyReviewsSummary}
                                />
                            </Col>
                        </Row>
                        <Row>
                            <Col md="12" className="m-0 p-0">

                                <ul className="nav nav-tabs">
                                    <li className="nav-item">
                                        <a className="nav-link active" data-toggle="tab" href="#locale-reviews-pane">{this.state.name} reviews</a>
                                    </li>
                                    <li className="nav-item">
                                        <a className="nav-link" data-toggle="tab" href="#tenancy-reviews-pane">Tenancy reviews</a>
                                    </li>
                                </ul>

                                <div className="tab-content bg-white rounded shadow-sm p-2 pt-4 mb-4">
                                    <div className="tab-pane active container" id="locale-reviews-pane">
                                        <h5 className="mb-1">Reviews of {this.state.name} from residents</h5>

                                        {this.state.localeReviews.length > 0 && this.state.localeReviews.map(
                                            ({ id, slug, author, title, content, overallStars, createdAt, positiveVotes }) => (
                                                <LocaleReview
                                                    {...this.props}
                                                    key={slug}
                                                    id={id}
                                                    slug={slug}
                                                    author={author}
                                                    title={title}
                                                    content={content}
                                                    overallStars={overallStars}
                                                    createdAt={createdAt}
                                                    positiveVotes={positiveVotes}
                                                    showVote={true}
                                                />
                                            )
                                        ).reduce((prev, curr) => [prev, <hr />, curr])}

                                        {this.state.localeReviews.length === 0 &&
                                            <Fragment>
                                                <hr />
                                                <p>
                                                    There are no reviews of {this.state.name} yet.
                                                </p>
                                            </Fragment>
                                        }
                                    </div>
                                    <div className="tab-pane container" id="tenancy-reviews-pane">
                                        <h5 className="mb-1">Property reviews from tenants in {this.state.name}</h5>

                                        {this.state.tenancyReviews.length > 0 && this.state.tenancyReviews.map(
                                            ({ id, author, start, end, title, content, property, branch, agency, stars, createdAt, comments, positiveVotes }) => (
                                                <Review
                                                    {...this.props}
                                                    key={id}
                                                    id={id}
                                                    author={author}
                                                    start={start}
                                                    end={end}
                                                    title={title}
                                                    content={content}
                                                    property={property}
                                                    branch={branch}
                                                    agency={agency}
                                                    stars={stars}
                                                    createdAt={createdAt}
                                                    comments={comments}
                                                    positiveVotes={positiveVotes}
                                                />
                                            )
                                        ).reduce((prev, curr) => [prev, <hr />, curr])}

                                        {this.state.tenancyReviews.length === 0 &&
                                            <Fragment>
                                                <hr />
                                                <p>
                                                    There are no tenant reviews yet for {this.state.name}.
                                                </p>
                                            </Fragment>
                                        }
                                    </div>
                                </div>

                            </Col>
                        </Row>
                        <Row>
                            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                                <h5 className="mb-4">Review {this.state.name}</h5>

                                <p className="mb-2">
                                    Are you a current or past resident of {this.state.name}?
                                    We'd love it if you could review your experience of living in {this.state.name}!
                                </p>
                                <hr />
                                {!this.state.reviewLocaleFormOpen &&
                                <Button onClick={this.openReviewLocaleForm} color="primary">Yes! I want to write a review</Button>
                                }
                                {this.state.reviewLocaleFormOpen &&
                                <ReviewLocaleForm
                                    localeName={this.state.name}
                                    localeSlug={this.props.match.params.slug}
                                    completedThankYou={this.localeReviewCompletedThankYou}
                                    {...this.props}
                                />
                                }
                            </Col>
                        </Row>
                    </div>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            name: data.name,
            content: data.content,
            localeReviews: data.localeReviews,
            tenancyReviews: data.tenancyReviews,
            agencyReviewsSummary: data.agencyReviewsSummary,
            loaded: true,
        });

        document.title = 'Top Lettings Agents in ' + this.state.name + ' | ' + Constants.SITE_NAME;
    }
}

export default LocaleView;
