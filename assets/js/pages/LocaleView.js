import React, {Fragment} from 'react';
import {Container, Col, Row} from 'reactstrap';
import Review from "../components/Review";
import RatedAgencies from "../components/RatedAgencies";
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";

class LocaleView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            content: '',
            reviews: [],
            agencyReviewsSummary: null,
            loaded: false,
        };

        this.loadData = this.loadData.bind(this);
    }

    render() {
        return (
            <Container>
                <DataLoader
                    url={'/api/l/' + this.props.match.params.slug}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                    <div>
                        <Row>
                            <Col md="12" className="page-title">
                                <h1>{this.state.name}</h1>
                            </Col>
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
                            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                                <h5 className="mb-1">Reviews from tenants</h5>

                                {this.state.tenancyReviews.map(
                                    ({ id, author, start, end, title, content, property, branch, agency, stars, createdAt, comments, positiveVotes }) => (
                                        <Fragment>
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
                                            >
                                            </Review>
                                            <hr />
                                        </Fragment>
                                    )
                                )}
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
            tenancyReviews: data.tenancyReviews,
            agencyReviewsSummary: data.agencyReviewsSummary,
            loaded: true,
        });

        document.title = 'Top Lettings Agents in ' + this.state.name + ' | ' + Constants.SITE_NAME;
    }
}

export default LocaleView;
