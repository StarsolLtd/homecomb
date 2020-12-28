import React, {Fragment} from 'react';
import {Container, Col, Row} from 'reactstrap';
import Review from "../components/Review";
import RatedAgencies from "../components/RatedAgencies";
import LoadingSpinner from "../components/LoadingSpinner";
import FileNotFound from "../errors/FileNotFound";

class LocaleView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            localeSlug: this.props.match.params.slug,
            name: '',
            content: '',
            reviews: [],
            agencyReviewsSummary: null,
            loaded: false,
            loading: false,
            loadingError: false,
            loadingErrorCode: null,
        };
    }

    componentDidMount() {
        this.fetchData();
    }

    render() {
        return (
            <Container>
                {this.state.loading &&
                    <LoadingSpinner />
                }
                {this.state.loadingError && this.state.loadingErrorCode === 404 &&
                    <FileNotFound />
                }
                {!this.state.loading && this.state.loaded &&
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

                                {this.state.reviews.map(
                                    ({ id, author, title, content, property, branch, agency, stars, createdAt }) => (
                                        <Fragment>
                                            <Review
                                                key={id}
                                                id={id}
                                                author={author}
                                                title={title}
                                                content={content}
                                                property={property}
                                                branch={branch}
                                                agency={agency}
                                                stars={stars}
                                                createdAt={createdAt}
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

    fetchData() {
        this.setState({loading: true});
        fetch(
            '/api/l/' + this.state.localeSlug,
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
            .then(
                response => {
                    this.setState({
                        loading: false,
                    })
                    if (!response.ok) {
                        this.setState({
                            loadingError: true,
                            loadingErrorCode: response.status,
                        })
                        return Promise.reject('Error: ' + response.status)
                    }
                    return response.json()
                }
            )
            .then(data => {
                this.setState({
                    name: data.name,
                    content: data.content,
                    reviews: data.reviews,
                    agencyReviewsSummary: data.agencyReviewsSummary,
                    loading: false,
                    loaded: true
                });
            });
    }
}

export default LocaleView;
