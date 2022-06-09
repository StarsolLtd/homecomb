import React from 'react';
import {shallow} from 'enzyme';
import LoadingSpinner from '../../assets/js/components/LoadingSpinner';

it('renders successfully and has a class supplied by a prop', () => {
    const wrapper = shallow(<LoadingSpinner className={'test-class'} />);
    expect(wrapper.find('.test-class').length).toBe(1);
});
