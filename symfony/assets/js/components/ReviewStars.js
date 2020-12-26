import React from 'react';
import Stars from "./Stars";

class ReviewStars extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            overall: this.props.overall || null,
            agency: this.props.agency || null,
            landlord: this.props.landlord || null,
            property: this.props.property || null,
        };
    }

    render() {
        return (
            <div className="review-stars">
                {this.state.overall &&
                    <Stars label="Overall" score={this.state.overall}/>
                }
                {this.state.agency &&
                    <Stars label="Agency" score={this.state.agency}/>
                }
                {this.state.landlord &&
                    <Stars label="Landlord" score={this.state.landlord}/>
                }
                {this.state.property &&
                    <Stars label="Property" score={this.state.property}/>
                }
            </div>
        );
    }
}

export default ReviewStars;