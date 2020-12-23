import React from 'react';
import ReactDOM from 'react-dom';
import PropertyAutocomplete from "./PropertyAutocomplete";

class Home extends React.Component {
    constructor() {
        super();
        this.state = {};
    }

    render() {
        return (
            <div>
                <h1 className="logo-large"><span className="red">Home</span><span className="bronze">Comb</span>
                </h1>
                <form>
                    <label htmlFor="propertySearch">Find a property</label>
                    <div className="form-group">
                        <PropertyAutocomplete
                            inputId="propertySearch"
                            source="/api/property/suggest-property"
                            placeholder="Start typing an address..."
                        />
                    </div>
                    <p id="propertySearchHelp" className="text-muted">After you've entered a few characters, you
                        will see suggested results</p>
                </form>
            </div>
        );
    }
}

ReactDOM.render(<Home />, document.getElementById('home-root'));