import React from 'react';
import {Container, Col, Row} from 'reactstrap';
import ReviewTenancyForm from "../components/ReviewTenancyForm";
import Constants from "../Constants";
import ReviewCompletedThankYou from "../content/ReviewCompletedThankYou";

class TenancyReview extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            completedThankYou: false,
        };

        this.completedThankYou = this.completedThankYou.bind(this);
    }

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | Review Tenancy';
    }

    completedThankYou() {
        this.setState({completedThankYou: true})
    }

    render() {
        if (this.state.completedThankYou) {
            return <ReviewCompletedThankYou />;
        }

        return (
            <Container>
                <Row>
                    <Col md="12" className="page-title">
                        <h1>Review your tenancy</h1>
                    </Col>
                </Row>

                <Row>
                    <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
                        <p>
                            Please complete the form below to submit your review:
                        </p>

                        <hr />

                        <ReviewTenancyForm
                            fixedBranch={false}
                            fixedProperty={false}
                            {...this.props}
                            completedThankYou={this.completedThankYou}
                        />
                    </Col>
                </Row>
            </Container>
        );
    }
}

export default TenancyReview;