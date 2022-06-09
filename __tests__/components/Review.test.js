import React from 'react'
import { shallow } from 'enzyme'
import Review from '../../assets/js/components/Review'

it('renders without crashing', () => {
  shallow(<Review />)
})
