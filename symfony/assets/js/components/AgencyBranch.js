import React from 'react';

class AgencyBranch extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            name: this.props.name,
            slug: this.props.slug,
            telephone: this.props.telephone,
            email: this.props.email,
        };
    }

    render() {
        return (
            <div className="agency-branch">
                <div><a href={'/branch/' + this.state.slug}>{this.state.name}</a></div>
                {this.state.telephone &&
                    <div className="telephone">Tel: {this.state.telephone}</div>
                }
                {this.state.email &&
                    <div className="email">Email: <a href={'mailto:' + this.state.email}>{this.state.email}</a></div>
                }
            </div>
        );
    }
}

export default AgencyBranch;