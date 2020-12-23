import React from 'react';

class AgencyBranch extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            name: this.props.name,
            slug: this.props.slug,
        };
    }

    render() {
        return (
            <p>
                <a href={'/branch/' + this.state.slug}>{this.state.name}</a>
            </p>
        );
    }
}

export default AgencyBranch;