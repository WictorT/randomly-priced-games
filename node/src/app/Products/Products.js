import React, {Component} from "react"
import {connect} from "react-redux"
import classNames from "classnames"
import _ from 'lodash'
import {fetchProducts} from "../../shared/store/actions/products"
import Loader from "../../shared/componets/UI/Loader/Loader"
import Button from "@material-ui/core/Button"
import Card from "@material-ui/core/Card"
import CardActions from "@material-ui/core/CardActions"
import CardContent from "@material-ui/core/CardContent"
import CardMedia from "@material-ui/core/CardMedia"
import Grid from "@material-ui/core/Grid"
import Typography from "@material-ui/core/Typography"
import Link from "@material-ui/core/Link"
import {withStyles} from "@material-ui/core/styles"
import InputBase from "@material-ui/core/InputBase"
import Select from "@material-ui/core/Select"
import MenuItem from "@material-ui/core/MenuItem"
import InputLabel from "@material-ui/core/InputLabel"

const BootstrapInput = withStyles(theme => ({
    root: {
        "label + &": {
            marginTop: theme.spacing.unit * 3,
        },
    },
    input: {
        borderRadius: 4,
        position: "relative",
        backgroundColor: theme.palette.background.paper,
        border: "1px solid #ced4da",
        fontSize: 16,
        width: "auto",
        padding: "10px 26px 10px 12px",
        transition: theme.transitions.create(["border-color", "box-shadow"]),
        // Use the system font instead of the default Roboto font.
        fontFamily: [
            "-apple-system",
            "BlinkMacSystemFont",
            "\"Segoe UI\"",
            "Roboto",
            "\"Helvetica Neue\"",
            "Arial",
            "sans-serif",
            "\"Apple Color Emoji\"",
            "\"Segoe UI Emoji\"",
            "\"Segoe UI Symbol\"",
        ].join(","),
        "&:focus": {
            borderRadius: 4,
            borderColor: "#80bdff",
            boxShadow: "0 0 0 0.2rem rgba(0,123,255,.25)",
        },
    },
}))(InputBase)

const styles = theme => ({
    layout: {
        width: "auto",
        marginLeft: theme.spacing.unit * 3,
        marginRight: theme.spacing.unit * 3,
        [theme.breakpoints.up(1100 + theme.spacing.unit * 3 * 2)]: {
            width: 1100,
            marginLeft: "auto",
            marginRight: "auto",
        },
    },
    cardGrid: {
        padding: `${theme.spacing.unit * 8}px 0`,
    },
    card: {
        height: "100%",
        display: "flex",
        flexDirection: "column",
    },
    cardMedia: {
        paddingTop: "56.25%", // 16:9
    },
    cardContent: {
        flexGrow: 1,
    },
    link: {
        marginRight: 10,
        padding: 10,
        fontSize: 16,
        color: "black",
        "&:hover": {
            background: "#DADADA",
            //color: "white",
            textDecoration: "none",
        },
    },
})

const optionsChangePerPage = [1, 3, 5]

class Products extends Component {

    componentDidMount() {
        this.props.fetchProducts()
    }

    handleChangePage = (event, per_page) => {
        const queryParams = {
            params: {
                page: event.target.id,
                per_page
            }
        }

        this.props.fetchProducts(queryParams)
    }

    handleChangePerPage = event => {
        const queryParams = {
            params: {
                per_page: event.target.value,
                page: 1
            }
        }

        this.props.fetchProducts(queryParams)
    }

    render() {
        const {
            classes,
            products,
            history,
            loading,
            totalPages,
            perPage
        } = this.props

        return (
            <React.Fragment>
                <main>
                    {
                        loading
                            ? <Loader/>
                            : <div className={classNames(classes.layout, classes.cardGrid)}>
                                <Grid container spacing={40}>
                                    {products.map(product => (
                                        <Grid item key={product.id} sm={6} md={4} lg={4}>
                                            <Card className={classes.card}>
                                                <CardMedia
                                                    className={classes.cardMedia}
                                                    image="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22288%22%20height%3D%22225%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20288%20225%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164edaf95ee%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164edaf95ee%22%3E%3Crect%20width%3D%22288%22%20height%3D%22225%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2296.32500076293945%22%20y%3D%22118.8%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                                                    title="Image title"
                                                />
                                                <CardContent className={classes.cardContent}>
                                                    <Typography gutterBottom variant="h5" component="h2">
                                                        {product.name}
                                                    </Typography>
                                                    <Typography>
                                                        This is a media card. You can use this section to describe the
                                                        content.
                                                    </Typography>
                                                </CardContent>
                                                <CardActions>
                                                    <Button
                                                        size="small"
                                                        color="primary"
                                                        onClick={() => history.push("/" + product.id)}
                                                    >
                                                        View
                                                    </Button>
                                                </CardActions>
                                            </Card>
                                        </Grid>
                                    ))}
                                </Grid>
                                {
                                    _.range(1, totalPages + 1).map(number => {
                                        return (
                                            <Link
                                                component="button"
                                                key={number}
                                                id={number}
                                                onClick={(event) => {this.handleChangePage(event, perPage)}}
                                                className={classes.link}
                                            >
                                                {number}
                                            </Link>
                                        )
                                    })
                                }
                                <InputLabel htmlFor="age-customized-select"></InputLabel>
                                <Select
                                    value={perPage}
                                    onChange={event => this.handleChangePerPage(event)}
                                    input={<BootstrapInput name="age" id="age-customized-select"/>}
                                >
                                    {
                                        optionsChangePerPage.map(number => {
                                            return (
                                                <MenuItem
                                                    component="button"
                                                    key={number}
                                                    value={number}
                                                >
                                                    {number}
                                                </MenuItem>
                                            )
                                        })
                                    }
                                </Select>
                            </div>
                    }
                </main>
            </React.Fragment>
        )
    }
}

function mapStateToProps(state) {
    return {
        products: state.products.products,
        loading: state.products.productsLoader,
        perPage: state.products.perPage,
        totalPages: state.products.totalPages
    }
}

function mapDispatchToProps(dispatch) {
    return {
        fetchProducts: (perPage, page) => dispatch(fetchProducts(perPage, page))
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(Products))