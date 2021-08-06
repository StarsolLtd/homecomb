import React from 'react';
import {Container, Col, Row, Breadcrumb, BreadcrumbItem} from 'reactstrap';
import ReviewTenancyForm from "../components/ReviewTenancyForm";
import Constants from "../Constants";
import ReviewCompletedThankYou from "../content/ReviewCompletedThankYou";
import {Link} from "react-router-dom";

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
                    <Breadcrumb className="w-100">
                        <BreadcrumbItem><Link to="/">{Constants.SITE_NAME}</Link></BreadcrumbItem>
                        <BreadcrumbItem className="active">Review your tenancy</BreadcrumbItem>
                    </Breadcrumb>
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