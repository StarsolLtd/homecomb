import React from 'react';
import {Col, Container, Row} from "reactstrap";
import DataLoader from "../components/DataLoader";
import Review from "../components/Review";

import '../../styles/latest-reviews.scss';

class LatestReviews extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            title: '',
            reviews: [],
            loaded: false,
        };
        this.loadData = this.loadData.bind(this);
    }

    render() {
        return (
            <div className="latest-reviews">
                <Container className="p-4 pt-5 pb-5">
                    <h2 className="text-center">Recent Reviews</h2>
                    <p className="text-center">
                        Here is what tenants have had to say about their renting experiences recently:
                    </p>
                    <Row>
                        <DataLoader
                            url={'/api/review/latest'}
                            loadComponentData={this.loadData}
                        />
                        {this.state.loaded &&
                            this.state.reviews.map(
                                ({ id, author, title, content, property, branch, agency, stars, createdAt }) => (
                                    <Col lg={4} md={6} sm={12} className="d-flex">
                                        <Col md={12} className="bg-white rounded shadow-sm pl-3 pr-4 m-1">
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
                                                showBranch={false}
                                                showOptions={false}
                                            />
                                        </Col>
                                    </Col>
                                )
                            )
                        }
                    </Row>
                </Container>
            </div>
        );
    }

    loadData(data) {
        this.setState({
            agency: data.title,
            reviews: data.reviews,
            loaded: true
        });

        console.log(this.state);
    }
}

export default LatestReviews;