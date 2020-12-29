import React from 'react';
import AgencyBranch from "../components/AgencyBranch";
import {Container} from "reactstrap";
import DataLoader from "../components/DataLoader";

class AgencyView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agency: {},
            loaded: false,
        };
        this.loadData = this.loadData.bind(this);
    }

    render() {
        return (
            <Container>
                <DataLoader
                    url={'/api/agency/' + this.props.match.params.slug}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                    <div>
                        <div className="col-md-12 page-title">
                            <h1>{this.state.agency.name}</h1>
                        </div>
                        <div className="col-md-12">
                            <div className="bg-white rounded shadow-sm p-4 mb-4 restaurant-detailed-ratings-and-reviews">
                                {this.state.agency.branches.map(
                                    ({ slug, name, telephone, email }) => (
                                        <AgencyBranch
                                            key={slug}
                                            slug={slug}
                                            name={name}
                                            telephone={telephone}
                                            email={email}
                                        >
                                        </AgencyBranch>
                                    )
                                )}
                            </div>
                        </div>
                    </div>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            agency: data,
            loaded: true
        });
    }
}

export default AgencyView;
