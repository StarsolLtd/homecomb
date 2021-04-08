import React, {Fragment} from 'react';
import {Redirect} from 'react-router-dom';

class AgentRoute extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const Component = this.props.render;
        return (
            <Fragment>
                {this.props.isAgencyAdmin &&
                    <Component {...this.props} />
                }
                {!this.props.isAgencyAdmin &&
                    <Redirect to={{pathname: '/verified/agency/create', state: {from: this.props.location}}} />
                }
            </Fragment>
        )
    }

}


export default AgentRoute;