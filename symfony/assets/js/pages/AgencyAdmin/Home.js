import React, {Fragment} from 'react';
import {Container, Row, Col} from 'reactstrap';
import DataLoader from "../../components/DataLoader";

class Home extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agency: null,
            branches: [],
            reviews: [],
            loaded: false,
        };

        this.loadData = this.loadData.bind(this);
    }

    render() {
        return (
            <Container>
                <DataLoader
                    url='/api/verified/agency-admin'
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                    <Fragment>
                        <Row>
                            <Col md="12" className="page-title">
                                <h1>{this.state.agency.name}</h1>
                            </Col>
                        </Row>

                        <Row>
                            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                                <h2 className="mb-1">Your branches</h2>

                            </Col>
                        </Row>

                        <Row>
                            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                                <h2 className="mb-1">Your reviews</h2>

                            </Col>
                        </Row>
                    </Fragment>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            agency: data.agency,
            branches: data.branches,
            reviews: data.reviews,
            loaded: true,
        });
    }
}

export default Home;