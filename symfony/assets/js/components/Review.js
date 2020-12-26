import React from 'react';

class Review extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            id: this.props.id,
            author: this.props.author,
            title: this.props.title,
            content: this.props.content,
            property: this.props.property,
            branch: this.props.branch,
            agency: this.props.agency,
            showProperty: this.props.showProperty || false,
            showBranch: this.props.showBranch || false,
            showAgency: this.props.showAgency || false
        };
    }

    render() {
        return (
            <div className="reviews-members pt-4 pb-4">
                {this.state.author}
            </div>
        );
    }
}

export default Review;