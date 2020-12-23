import React from 'react';
import ReactDOM from 'react-dom';

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
                        <input type="text" className="form-control" id="propertySearch"
                               aria-describedby="propertySearchHelp" placeholder="Start typing an address" />
                    </div>
                    <p id="propertySearchHelp" className="text-muted">After you've entered a few characters, you
                        will see suggested results</p>
                </form>
            </div>
        );
    }
}

ReactDOM.render(<Home />, document.getElementById('home-root'));