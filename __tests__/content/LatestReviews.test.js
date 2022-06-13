import React from 'react'
import { shallow } from 'enzyme'
import LatestReviews from '../../assets/js/content/LatestReviews'

it('renders without crashing', () => {
  shallow(<LatestReviews />)
})
