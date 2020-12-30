import React, {Fragment} from 'react';
import {Row, Col} from 'reactstrap';
import DataLoader from "../../components/DataLoader";
import ReactTable from 'react-table-v6'
import 'react-table-v6/react-table.css'

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
            <Fragment>
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

                        <Row className="bg-white rounded shadow-sm p-4 mb-4">
                            <Col md={6} sm={12} className="mb-4">
                                <h2 className="mb-4">Reviews of {this.state.agency.name}</h2>
                                <ReactTable
                                    data={this.state.reviews}
                                    columns={[
                                        {Header: "Author", accessor: "author"},
                                        {Header: "Property", accessor: "property.addressLine1"},
                                    ]}
                                    defaultPageSize={10}
                                    showPageSizeOptions={false}
                                    className="-striped -highlight"
                                />
                            </Col>
                            <Col md={6} sm={12} className="mb-4">
                                <h2 className="mb-4">Branches of {this.state.agency.name}</h2>
                                <ReactTable
                                    data={this.state.branches}
                                    columns={[
                                        {Header: "Name", accessor: "name"},
                                    ]}
                                    defaultPageSize={10}
                                    showPageSizeOptions={false}
                                    className="-striped -highlight"
                                />
                            </Col>
                        </Row>
                    </Fragment>
                }
            </Fragment>
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